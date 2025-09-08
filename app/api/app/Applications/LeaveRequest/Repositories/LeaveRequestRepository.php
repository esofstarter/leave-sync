<?php

namespace App\Applications\LeaveRequest\Repositories;

use App\Applications\Country\Model\Country;
use App\Applications\User\Model\User;
use App\Applications\LeaveRequest\DTO\LeaveRequestDTO;
use App\Applications\LeaveRequest\Mail\{
    LeaveRequestNotification,
    LeaveRequestNotificationUpdate,
    LeaveRequestConfirmation,
    LeaveRequestDeclining,
    LeaveRequestCancelation,
    LeaveRequestConfirmationPDF,
    LeaveRequestConfirmationUpdate
};
use App\Applications\Pagination\StarterPaginator;
use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\Document\Model\Document;
use App\Applications\NationalHoliday\Model\NationalHoliday;
use Illuminate\Support\Facades\{Mail, Auth};
use DateTime;
use setasign\Fpdi\Fpdi;
use Storage;
use Illuminate\Support\Facades\Route;
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;

/**
 * @property LeaveRequest $leaveRequest
 * @property User $user
 */
class LeaveRequestRepository implements LeaveRequestRepositoryInterface
{
    public function __construct(
        LeaveRequest $leaveRequest,
        User $user
    ) {
        $this->leaveRequest = $leaveRequest;
        $this->user = $user;
    }

    private const COLUMNS_MAP = [
        'leave_type_id' => 'leave_requests.leave_type_id',
        'request_to' => 'leave_requests.request_to',
        'status' => 'leave_requests.is_confirmed',
        'days' => 'leave_requests.days',
        'start_date' => 'leave_requests.start_date',
        'end_date' => 'leave_requests.end_date'
    ];

    // Get all leaveRequests
    public function getAll(): array
    {
        return LeaveRequestDTO::fromCollection($this->leaveRequest::all());
    }

    public function getApproved(): array
    {
        $query = $this->leaveRequest->with(['user', 'leaveType']);
        
        $query->where('is_confirmed', '=', 2);

        return $query->whereNull('deleted_at')->get()->toArray();
    }

    public function getApprovedByUser(): array
    {
        $userId = Route::current()->parameter('id');
        
        $query = $this->leaveRequest->with(['user', 'leaveType']);
        
        $query->where('is_confirmed', '=', 2)->where('user_id', $userId);

        return $query->whereNull('deleted_at')->get()->toArray();
    }

    public function getPending(): array
    {
        $query = $this->leaveRequest->with(['user', 'leaveType']);
        
        $user = Auth::user();
        $query->where('request_to', '=', $user->id);

        $query->where('is_confirmed', '=', 0);

        return $query->whereNull('deleted_at')->get()->toArray();
    }
    // Get Leave Request by Id
    public function get($id): LeaveRequest
    {
        return $this->leaveRequest::findOrFail($id);
    }

    // Create a new request
    public function create(LeaveRequestDTO $leaveRequestDTO): LeaveRequest
    {
        $user = User::find($leaveRequestDTO->user_id);
        $leaveDays = $this->calculateDays($leaveRequestDTO, $user);
        $leaveRequestDTO->days = $leaveDays;
        $leaveRequest = $this->leaveRequest->create($leaveRequestDTO->toArray());
        if ($leaveRequest->leave_type_id == 6) {
            $this->confirm($leaveRequest->id, $leaveRequestDTO, 2, false);
        } else {
            $this->sendRequestEmail($leaveRequest, false);
        }

        return $leaveRequest;
    }

    public function update(int $leaveRequestId, LeaveRequestDTO $leaveRequestData): LeaveRequest
    {
        $leaveRequest = $this->get($leaveRequestId);
        $leaveRequestData->is_confirmed = 0;
        $leaveDays = $this->calculateDays($leaveRequestData, $leaveRequest->user);
        $leaveRequestData->days = $leaveDays;
        $leaveRequest->update([...$leaveRequestData->toArray(), 'user_id' => $leaveRequest->user_id]);
        $this->sendRequestEmail($leaveRequest, true);
        return $leaveRequest;
    }

    public function delete(int $id)
    {
        $user = Auth::user();
        $leaveRequest = $this->leaveRequest::findOrFail($id);
        if ($leaveRequest->is_confirmed == 2 && $user->role == 1 && $leaveRequest->leave_type_id == 3) {
            $leaveRequestUser = User::find($leaveRequest->user->id);
            $leaveRequestUser->update([
                'paid_leaves_left' => $leaveRequest->user->paid_leaves_left + $leaveRequest->days
            ]);
        }
        $this->sendRequestCancelationEmail($leaveRequest);

        return $leaveRequest->delete();
    }

    public function confirm(int $leaveRequestId, LeaveRequestDTO $leaveRequestData, int $isConfirmed, bool $isUpdate): LeaveRequest
    {
        $leaveRequest = $this->get($leaveRequestId);
        $user = Auth::user();
        $leaveRequestUser = User::find($leaveRequest->user->id);

        if ($isConfirmed == 2 && $isUpdate && $leaveRequest->leave_type_id == 3) {
            $leaveRequestUser->update([
                'paid_leaves_left' => $leaveRequestUser->paid_leaves_left + $leaveRequest->days
            ]);
        }

        $leaveDays = $this->calculateDays($leaveRequestData, $leaveRequest->user);

        $leaveRequest->update([
            ...$leaveRequestData->toArray(),
            'is_confirmed' => $isConfirmed,
            'confirmed_by' => $user->id,
            'days' => $leaveDays,
            'user_id' => $leaveRequest->user_id
        ]);

        if ($isConfirmed == 2) {
            if (in_array($leaveRequest->leave_type_id, [3, 4])) {
                $this->createLeaveRequestPDF($leaveRequest->user, $leaveRequest);
            }

            if ($leaveRequest->user && $leaveRequestData->leave_type_id == 3) {
                $leaveRequestUser->update([
                    'paid_leaves_left' => max(0, $leaveRequestUser->paid_leaves_left - $leaveDays)
                ]);
            }
        }

        $this->sendRequestConfirmationEmail($leaveRequest, $isUpdate);
        if ($leaveRequestUser->country == 1) {
            $this->sendConfirmationAccountentsEmail($leaveRequest);
        }
        // $this->createRedmineIssueOnConfirm($leaveRequest);
        return $leaveRequest;
    }

    public function draw($data): StarterPaginator
    {
        $user = Auth::user();
        $query = $this->leaveRequest->with(['user', 'leaveType']);

        if (isset(self::COLUMNS_MAP[$data['column']])) {
            $query->orderBy(self::COLUMNS_MAP[$data['column']], $data['dir']);
        } else {
            $query->orderBy('leave_requests.created_at', 'desc'); // Default to latest requests first
        };
        // Search by keyword regardless on user first name last name or email and leave type
        if ($search = $data['search']) {
            $query->where(function ($q) use ($search) {
                $q->where('leave_requests.reason', 'like', "%$search%")
                    ->orWhereHas('user', fn($q) => $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%"))
                    ->orWhereHas('leaveType', fn($q) => $q->where('name', 'like', "%$search%"));
            });
        }

        $isList = $data['isList'];
        if($isList !== "false") {
            $user = Auth::user();
            $userRole = $user->getRoleAttribute(); 
            if ($userRole == 2) {
                $query->whereHas('user', function ($q) {
                    $q->whereHas('roles', function ($roleQuery) {
                        $roleQuery->where('roles.id', 3);
                    });
                });
            };
        } else {
            $this->applyUserRoleFilter($query, $user, $data['userId'] ?? null);
        }

        return $query->whereNull('deleted_at')->paginate($data['length']);
    }

    private function applyUserRoleFilter($query, $user, $calendarUserId)
    {
        $roleId = $user->getRoleAttribute();

        if ($calendarUserId) {
            $query->where('leave_requests.user_id', $calendarUserId)
                  ->where('leave_requests.is_confirmed', 2);
        } else {
            $query->where('leave_requests.user_id', $user->id);
        }
    }

    private function sendRequestEmail(LeaveRequest $leaveRequest, bool $isUpdate)
    {
        $recipients = $this->getRecipients($leaveRequest);

        $mailClass = $isUpdate ? LeaveRequestNotificationUpdate::class : LeaveRequestNotification::class;
        Mail::to($recipients)->send(new $mailClass($leaveRequest));
    }

    private function sendRequestConfirmationEmail(LeaveRequest $leaveRequest, bool $isUpdate)
    {
        $recipients = $this->getRecipients($leaveRequest);
        $document = Document::where('leave_request_id', $leaveRequest->id)->first();
        $documentPath = $document ? Storage::disk('public')->path($document->file_path) : null;

        if ($isUpdate) {
            Mail::to($recipients)->send(new LeaveRequestConfirmationUpdate($leaveRequest, $documentPath));
            return;
        }

        match ($leaveRequest->is_confirmed) {
            1 => Mail::to($recipients)->send(new LeaveRequestDeclining($leaveRequest)),
            2 => Mail::to($recipients)->send(new LeaveRequestConfirmation($leaveRequest, $documentPath)), // With attachment
            default => null,
        };
    }

    private function sendRequestCancelationEmail(LeaveRequest $leaveRequest)
    {
        $recipients = $this->getRecipients($leaveRequest);

        Mail::to($recipients)->send(new LeaveRequestCancelation($leaveRequest));
    }


    private function sendConfirmationAccountentsEmail(LeaveRequest $leaveRequest) {
        $administrationEmails = User::role(User::COLLABORATOR)
        ->where('country', '=', $leaveRequest->user->country)
        ->pluck('email')
        ->toArray(); 

        if ($leaveRequest->is_confirmed == 2) {
            $document = Document::where('leave_request_id', $leaveRequest->id)->first();
            Mail::to($administrationEmails)->send(new LeaveRequestConfirmationPDF($leaveRequest, $document ? Storage::disk('public')->path($document->file_path) : null));
        } 
    }

    private function getRecipients(LeaveRequest $leaveRequest)
    {
        $requestedUser = $leaveRequest->user;
        $requestToUserEmail = $leaveRequest->requestToUser->email;
        if ($leaveRequest->is_confirmed == 2) {
            return array_filter([
                $requestedUser?->email,
                $requestToUserEmail,
                ...User::role(User::ADMIN)->pluck('email')->toArray()
            ]);
        } else if ($leaveRequest->is_confirmed == 1) {
            return $requestedUser->email;
        } else if ($leaveRequest->is_confirmed == 0) {
            return $requestToUserEmail;
        }

        
    }
    
    private function createLeaveRequestPDF(User $user, LeaveRequest $leaveRequest): void
    {
        // 1) Transliterate to Cyrillic (requires PHP intl)
        $toCyr = function (string $s): string {
            if (class_exists(\Transliterator::class)) {
                // You can try 'Latin-Cyrillic/BGN' for Bulgarian,
                // or just 'Latin-Cyrillic' which works well for mk/bg.
                return \Transliterator::create('Latin-Cyrillic')->transliterate($s);
            }
            return $s; // fallback: leave as-is
        };

        $fullNameCyr = $toCyr($user->first_name).' '.$toCyr($user->last_name);
        $positionCyr = $toCyr((string)($user->position ?? ''));
        $fullNameRequestCyr = $toCyr($leaveRequest->requestToUser->first_name).' '.$toCyr($leaveRequest->requestToUser->last_name);

        $isSingleDay = $leaveRequest->end_date === null;
        $nowDate     = $this->formatDate(now());
        $start_date  = $this->formatDate($leaveRequest->start_date);
        $end_date    = $leaveRequest->end_date ? $this->formatDate($leaveRequest->end_date) : $start_date;

        $leaveDays = $isSingleDay ? 1 : $this->calculateDays($leaveRequest, $user);
        $userCountry = (int) $leaveRequest->user->country;

        // 2) Use TCPDF-backed FPDI
        $pdf = new TcpdfFpdi();

        // optional: remove TCPDF default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        // 3) Import your existing PDF template as before
        if ($userCountry === 1) {
            $pdf->setSourceFile(public_path($leaveRequest->leave_type_id == 3 ? 'MK_template_paid.pdf' : 'MK_template_unpaid.pdf'));
        } else {
            $pdf->setSourceFile(public_path($leaveRequest->leave_type_id == 3 ? 'BG_template_paid.pdf' : 'BG_template_unpaid.pdf'));
        }
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx, 0, 0, 210);

        // 4) Set a Unicode font that includes Cyrillic
        // TCPDF ships 'dejavusans' & 'dejavusanscondensed'
        $pdf->SetFont('dejavusans', '', 11, '', true); // <- UTF-8 + Cyrillic OK

        // 5) Write text (now Cyrillic works)
        if ($userCountry !== 1) {
            $pdf->SetXY(111, 76);
            $pdf->Write(0, $fullNameCyr);   
            $pdf->SetXY(129, 82);
            $pdf->Write(0, $positionCyr); 
            $pdf->SetXY(115, 88);
            $pdf->Write(0, $leaveRequest->user->private_id);                // ← Cyrillic
            $pdf->SetXY($leaveRequest->leave_type_id == 3 ? 92 : 80, 132);
            $pdf->Write(0, (string)($leaveDays ?? 'N/A'));
            $pdf->SetXY(44, 138);
            $pdf->Write(0, $start_date ?? 'N/A');
            $pdf->SetXY(44, 145);
            $pdf->Write(0, $end_date ?? '');
            $pdf->SetXY(24, 210);
            $pdf->Write(0, $nowDate ?? 'N/A');
            $pdf->SetXY(96, 236);
            $pdf->Write(0, $fullNameRequestCyr ?? 'N/A');
            $pdf->SetXY(24, 241);
            $pdf->Write(0, $nowDate ?? 'N/A');
        } else {
            $pdf->SetXY(111, 77);
            $pdf->Write(0, $fullNameCyr);                // ← Cyrillic
            $pdf->SetXY(65, 118);
            $pdf->Write(0, (string)($leaveDays ?? 'N/A'));
            $pdf->SetXY(124, 118);
            $pdf->Write(0, $start_date ?? 'N/A');
            $pdf->SetXY(150, 118);
            $pdf->Write(0, $end_date ?? '');
            $pdf->SetXY(24, 193);
            $pdf->Write(0, $nowDate ?? 'N/A');
        }

        // Save file (same as before)
        $pdfDirectory = storage_path("app/public/");
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0777, true);
        }
        $fileName = $user->first_name . "_" . $user->last_name ."_" . str_replace('-', '_', $leaveRequest->start_date) . ".pdf";
        $pdfPath  = $fileName;
        $fullPath = $pdfDirectory . $pdfPath;

        $pdf->Output($fullPath, 'F');

        Document::create([
            'user_id'          => $leaveRequest->user_id,
            'leave_request_id' => $leaveRequest->id,
            'file_path'        => $pdfPath,
            'file_name'        => $fileName
        ]);
    }

    
    private function formatDate(string $date): string
    {
        return date('d.m.Y', strtotime($date));
    }

    private function calculateDays($leaveRequest, $user) {
        // Handle single-day requests
        if ($leaveRequest->end_date === null) {
            return 1;
        }

        $startDate = new DateTime($leaveRequest->start_date);
        $endDate = new DateTime($leaveRequest->end_date);
    
        // Calculate the difference in days (inclusive of both start and end dates)
        $interval = $startDate->diff($endDate);
        $totalDays = $interval->days + 1; // +1 because both start and end dates are inclusive
        
        $country = Country::find($user->country);
    
        $nationalHolidays = NationalHoliday::whereYear('date', $startDate->format('Y'))
            ->where('country', $country->name)
            ->pluck('date')
            ->toArray();
        
        // Count weekends and holidays in the date range
        $weekendHolidayDays = 0;
        $currentDate = clone $startDate;
        
        for ($i = 0; $i < $totalDays; $i++) {
            $dateString = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N'); // 1=Monday, 7=Sunday
            
            if ($dayOfWeek == 6 || $dayOfWeek == 7 || in_array($dateString, $nationalHolidays)) {
                $weekendHolidayDays++;
            }
            
            $currentDate->modify('+1 day');
        }
        
        $leaveDays = $totalDays - $weekendHolidayDays;
    
        return max(1, $leaveDays); // ✅ Ensure at least 1 day is counted
    }

    // Add Issue To REDMINE:
    public function createRedmineIssueOnConfirm($leaveRequest)
    {
        $formattedStartDate = \Carbon\Carbon::parse($leaveRequest->start_date)->format('d M Y');
        $formattedEndDate = $leaveRequest->end_date
            ? \Carbon\Carbon::parse($leaveRequest->end_date)->format('d M Y')
            : null;
            
        $apiUrl = env('REDMINE_API_URL');
        $apiKey = env('REDMINE_API_KEY');

        $data = [
            "issue" => [
                "project_id" => 'p000-holday',
                "subject" => $leaveRequest->user->first_name . ' ' . $leaveRequest->user->last_name . ': ' . $leaveRequest->leaveType->name .  ': ' . $leaveRequest->days . ' days: '. $formattedStartDate . ($leaveRequest->end_date ? ' to ' . $formattedEndDate : ''),
                "status_id" => 5 
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-Redmine-API-Key: $apiKey"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $httpCode !== 201) {
            $errorMessage = curl_error($ch);
            \Log::error("Failed to create Redmine issue. Error: " . $errorMessage . " Response: " . $response);
            throw new \Exception("Redmine issue creation failed. HTTP Code: $httpCode. Error: $errorMessage");
        }

        curl_close($ch);

        \Log::info("Redmine issue created successfully. Response: " . $response);
        return json_decode($response, true);
    }

}
