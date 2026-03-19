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
        if($user->role !== 1) {
            $query->where('request_to', '=', $user->id);
        }

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
        if ($leaveRequestData->is_confirmed === null) {
            $leaveRequestData->is_confirmed = $leaveRequest->is_confirmed;
        }
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
            $mail = Mail::to($recipients)->send(new LeaveRequestConfirmationUpdate($leaveRequest, $documentPath));
            return;
        }

        $mailable = match ($leaveRequest->is_confirmed) {
            1 => new LeaveRequestDeclining($leaveRequest),
            2 => new LeaveRequestConfirmation($leaveRequest, $documentPath),
            default => null,
        };
        
        if ($mailable) {
            $mail = Mail::to($recipients);
            
            // CC collaborators for  approved vacation/sick leave requests
            if ($leaveRequest->is_confirmed == 2 && in_array($leaveRequest->leave_type_id, [1, 2, 3, 4])) {
                $collaboratorEmails = User::role(User::COLLABORATOR)
                    ->withoutTrashed()
                    ->pluck('email')
                    ->toArray();
                
                if (!empty($collaboratorEmails)) {
                    $mail = $mail->cc($collaboratorEmails);
                }
            }
            
            $mail->send($mailable);
        }
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
                ...User::role(User::ADMIN)->where('email', '!=', 'admin@esof.net')->pluck('email')->toArray()
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
        $toCyr = static function (string $s): string {
            if (class_exists(\Transliterator::class)) {
                $tr = \Transliterator::create('Latin-Cyrillic');
                return $tr ? (string) $tr->transliterate($s) : $s;
            }
            return $s;
        };

        $fullNameCyr = trim($toCyr((string) $user->first_name) . ' ' . $toCyr((string) $user->last_name));
        $positionCyr = trim($toCyr((string) ($user->position ?? '')));

        $requestToUser = $leaveRequest->requestToUser; // assumes relation is loaded/available
        $fullNameRequestCyr = $requestToUser
            ? trim($toCyr((string) $requestToUser->first_name) . ' ' . $toCyr((string) $requestToUser->last_name))
            : '';

        $isSingleDay = $leaveRequest->end_date === null;

        $nowDate    = $this->formatDate(now());
        $start_date = $this->formatDate($leaveRequest->start_date);
        $end_date   = $leaveRequest->end_date ? $this->formatDate($leaveRequest->end_date) : $start_date;

        $leaveDays   = $isSingleDay ? 1 : $this->calculateDays($leaveRequest, $user);
        $userCountry = (int) $leaveRequest->user->country;

        // 2) Choose template
        if ($userCountry === 1) {
            $templatePath = public_path($leaveRequest->leave_type_id == 3 ? 'MK_template_paid.pdf' : 'MK_template_unpaid.pdf');
        } else {
            $templatePath = public_path($leaveRequest->leave_type_id == 3 ? 'BG_template_paid.pdf' : 'BG_template_unpaid.pdf');
        }

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("PDF template missing: {$templatePath}");
        }

        // 3) Use TCPDF-backed FPDI but hard-disable Header/Footer by overriding methods
        $pdf = new class extends TcpdfFpdi {
            public function Header(): void {}
            public function Footer(): void {}
        };

        // Make sure TCPDF doesn't add anything + lock geometry
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setHeaderMargin(0);
        $pdf->setFooterMargin(0);

        // 4) Import template FIRST, then create a page that matches the template size exactly (prevents scaling/coordinate drift)
        $pageCount = $pdf->setSourceFile($templatePath);
        if ($pageCount < 1) {
            throw new \RuntimeException("Template has no pages: {$templatePath}");
        }

        $tplIdx = $pdf->importPage(1);
        $tplSize = $pdf->getTemplateSize($tplIdx); // width/height in the current unit

        // Create page exactly matching template
        $pdf->AddPage($tplSize['orientation'], [$tplSize['width'], $tplSize['height']]);
        $pdf->useTemplate($tplIdx, 0, 0, $tplSize['width'], $tplSize['height'], true);

        // 5) Cyrillic-capable font + slightly smaller size
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Helper: bounded text placement (prevents long text spilling into other fields)
        $put = static function (TcpdfFpdi $pdf, float $x, float $y, float $w, string $text, string $align = 'L', float $fontSize = 10.0): void {
            $pdf->SetFont('dejavusans', '', $fontSize, '', true);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($w, 0, $text, 0, $align, false, 1, '', '', true, 0, false, true, 0, 'T', false);
        };

        // Helper: short fixed fields
        $putCell = static function (TcpdfFpdi $pdf, float $x, float $y, float $w, string $text, string $align = 'C', float $fontSize = 10.0): void {
            $pdf->SetFont('dejavusans', '', $fontSize, '', true);
            $pdf->SetXY($x, $y);
            $pdf->Cell($w, 0, $text, 0, 0, $align, false);
        };

        // 6) Write text per template (start from your original coordinates; reduce font for name if needed)
        if ($userCountry === 1) {
            // MK TEMPLATE (revert to your original block, only smaller font + bounded name)
            $put($pdf, 112 , 73, 90, $fullNameCyr, 'L', 9);

            $putCell($pdf, 56, 116.5, 18, (string) ($leaveDays ?? 'N/A'), 'C', 8.5);
            $putCell($pdf, 121, 116.5, 28, (string) ($start_date ?? 'N/A'), 'C', 8.5);
            $putCell($pdf, 146, 116.5, 28, (string) ($end_date ?? ''), 'C', 8.5);

            $put($pdf, 24, 193, 55, (string) ($nowDate ?? 'N/A'), 'L', 10);
        } else {
            // BG TEMPLATE (your original positions, but bounded)
            $put($pdf, 111, 77, 90, $fullNameCyr, 'L', 8.5);
            $put($pdf, 129, 82, 70, $positionCyr, 'L', 8.5);

            $put($pdf, 115, 88, 60, (string) $leaveRequest->user->private_id, 'L', 10);

            $putCell($pdf, ($leaveRequest->leave_type_id == 3 ? 92 : 80), 132, 20, (string) ($leaveDays ?? 'N/A'), 'C', 10);

            $putCell($pdf, 42, 138, 30, (string) ($start_date ?? 'N/A'), 'C', 9.5);
            $putCell($pdf, 42, 145, 30, (string) ($end_date ?? ''), 'C', 9.5);

            $put($pdf, 24, 210, 45, (string) ($nowDate ?? 'N/A'), 'L', 10);
            $put($pdf, 96, 236, 100, (string) ($fullNameRequestCyr ?? 'N/A'), 'L', 9.5);
            $put($pdf, 24, 241, 45, (string) ($nowDate ?? 'N/A'), 'L', 10);
            
        }

        // 7) Save file
        $pdfDirectory = storage_path('app/public/');
        if (!is_dir($pdfDirectory) && !mkdir($pdfDirectory, 0755, true) && !is_dir($pdfDirectory)) {
            throw new \RuntimeException("Unable to create PDF directory: {$pdfDirectory}");
        }
        if (!is_writable($pdfDirectory)) {
            throw new \RuntimeException("PDF directory not writable: {$pdfDirectory}");
        }

        // Safer filename (avoid Cyrillic filesystem edge cases)
        $safeBaseName = \Illuminate\Support\Str::slug($user->first_name . ' ' . $user->last_name, '_');
        $fileName     = $safeBaseName . '_' . str_replace('-', '_', (string) $leaveRequest->start_date) . '.pdf';

        $pdfPath  = $fileName;
        $fullPath = $pdfDirectory . $pdfPath;

        $pdf->Output($fullPath, 'F');

        clearstatcache(true, $fullPath);
        if (!file_exists($fullPath) || filesize($fullPath) === 0) {
            throw new \RuntimeException("PDF not written or empty at: {$fullPath}");
        }

        Document::create([
            'user_id'          => $leaveRequest->user_id,
            'leave_request_id' => $leaveRequest->id,
            'file_path'        => $pdfPath,
            'file_name'        => $fileName,
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
