<?php

namespace App\Applications\LeaveRequest\Services;

use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\RedmineTaskMapping\Model\RedmineTaskMapping;
use App\Applications\Country\Model\Country;
use App\Applications\NationalHoliday\Model\NationalHoliday;
use DateTime;
use Illuminate\Support\Facades\Log;

class RedmineTimeLoggerService implements RedmineTimeLoggerServiceInterface
{
    /**
     * Log time entries in Redmine for an approved leave request
     * 
     * @param LeaveRequest $leaveRequest
     * @return bool
     */
    public function logLeaveTimeEntries(LeaveRequest $leaveRequest): bool
    {
        // Skip if already logged
        if ($leaveRequest->redmine_logged) {
            Log::info("Redmine time already logged for leave request ID: {$leaveRequest->id}");
            return true;
        }

        // Get the Redmine task ID for this leave type
        $taskId = $this->getRedmineTaskId($leaveRequest->leave_type_id);
        
        if (!$taskId) {
            Log::warning("No Redmine task mapping found for leave_type_id: {$leaveRequest->leave_type_id}");
            return false;
        }

        // Get working days for the leave period
        $workingDays = $this->getWorkingDays($leaveRequest);
        
        if (empty($workingDays)) {
            Log::warning("No working days found for leave request ID: {$leaveRequest->id}");
            return false;
        }

        // Build comment for time entries
        $comment = $this->buildComment($leaveRequest);

        // Log time for each working day
        $allSuccess = true;
        foreach ($workingDays as $date) {
            $success = $this->createTimeEntry($taskId, $date, $comment);
            if (!$success) {
                $allSuccess = false;
                Log::error("Failed to create Redmine time entry for date: {$date}, leave request ID: {$leaveRequest->id}");
            }
        }

        // Mark as logged if all entries were successful
        if ($allSuccess) {
            $leaveRequest->update(['redmine_logged' => true]);
            Log::info("Successfully logged {count} time entries for leave request ID: {$leaveRequest->id}", [
                'count' => count($workingDays)
            ]);
        }

        return $allSuccess;
    }

    /**
     * Delete time entries from Redmine for a leave request
     * (Placeholder for future implementation)
     * 
     * @param LeaveRequest $leaveRequest
     * @return bool
     */
    public function deleteLeaveTimeEntries(LeaveRequest $leaveRequest): bool
    {
        // TODO: Implement deletion logic
        // This would require:
        // 1. Query Redmine API to find time entries matching the comment pattern
        // 2. Delete each time entry via DELETE /time_entries/{id}.json
        
        Log::info("Delete time entries called for leave request ID: {$leaveRequest->id} (not implemented yet)");
        
        return true;
    }

    /**
     * Get the Redmine task ID for a given leave type
     * 
     * @param int $leaveTypeId
     * @return int|null
     */
    private function getRedmineTaskId(int $leaveTypeId): ?int
    {
        $mapping = RedmineTaskMapping::where('leave_type_id', $leaveTypeId)
            ->where('is_active', true)
            ->first();

        return $mapping ? $mapping->redmine_task_id : null;
    }

    /**
     * Get array of working days (YYYY-MM-DD) for the leave period
     * Excludes weekends and national holidays
     * 
     * @param LeaveRequest $leaveRequest
     * @return array
     */
    private function getWorkingDays(LeaveRequest $leaveRequest): array
    {
        // Handle single-day requests
        if ($leaveRequest->end_date === null) {
            return [$leaveRequest->start_date];
        }

        $startDate = new DateTime($leaveRequest->start_date);
        $endDate = new DateTime($leaveRequest->end_date);
        
        // Get country for holiday checking
        $country = Country::find($leaveRequest->user->country);
        
        // Get national holidays for the year
        $nationalHolidays = NationalHoliday::whereYear('date', $startDate->format('Y'))
            ->where('country', $country->name)
            ->pluck('date')
            ->toArray();
        
        // Build array of working days
        $workingDays = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N'); // 1=Monday, 7=Sunday
            
            // Include only working days (not weekends or holidays)
            if ($dayOfWeek != 6 && $dayOfWeek != 7 && !in_array($dateString, $nationalHolidays)) {
                $workingDays[] = $dateString;
            }
            
            $currentDate->modify('+1 day');
        }
        
        return $workingDays;
    }

    /**
     * Create a single time entry in Redmine
     * 
     * @param int $taskId
     * @param string $date Date in YYYY-MM-DD format
     * @param string $comment
     * @param float $hours
     * @return bool
     */
    private function createTimeEntry(int $taskId, string $date, string $comment, float $hours = 8.0): bool
    {
        $apiUrl = env('REDMINE_API_URL');
        $apiKey = env('REDMINE_API_KEY');
        $botUserId = env('REDMINE_BOT_USER_ID');
        $activityId = env('REDMINE_DEFAULT_ACTIVITY_ID', 9);

        if (!$apiUrl || !$apiKey) {
            Log::error("Redmine API credentials not configured");
            return false;
        }

        $data = [
            "time_entry" => [
                "issue_id" => $taskId,
                "hours" => $hours,
                "spent_on" => $date,
                "user_id" => (int) $botUserId,
                "activity_id" => (int) $activityId,
                "comments" => $comment
            ]
        ];

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $apiUrl . '/time_entries.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-Redmine-API-Key: {$apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        if ($response === false || $httpCode !== 201) {
            Log::error("Failed to create Redmine time entry", [
                'http_code' => $httpCode,
                'response' => $response,
                'date' => $date,
                'task_id' => $taskId
            ]);
            return false;
        }

        return true;
    }

    /**
     * Build a comment string for the time entry
     * Format: "John Doe - Sick Leave (Paid)"
     * 
     * @param LeaveRequest $leaveRequest
     * @return string
     */
    private function buildComment(LeaveRequest $leaveRequest): string
    {
        $user = $leaveRequest->user;
        $leaveType = $leaveRequest->leaveType;
        
        $fullName = trim($user->first_name . ' ' . $user->last_name);
        $leaveTypeName = $leaveType->name;
        
        return "{$fullName} - {$leaveTypeName}";
    }
}
