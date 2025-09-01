<?php

namespace App\Applications\LeaveRequest\Mail;

use App\Applications\LeaveRequest\Model\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public LeaveRequest $leaveRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $formattedStartDate = \Carbon\Carbon::parse($this->leaveRequest->start_date)->format('d M Y');
        $formattedEndDate = $this->leaveRequest->end_date
            ? \Carbon\Carbon::parse($this->leaveRequest->end_date)->format('d M Y')
            : null;
        $subject =$this->leaveRequest->user->first_name . ' ' . $this->leaveRequest->user->last_name . ': ' . $this->leaveRequest->leaveType->name .  ': ' . $this->leaveRequest->days . ' days: ' . $formattedStartDate . ($this->leaveRequest->end_date ? ' to ' . $formattedEndDate : '');
        return $this->subject('Requested: ' . $subject)
                    ->replyTo($this->leaveRequest->user->email, $this->leaveRequest->user->first_name . ' ' . $this->leaveRequest->user->last_name)
                    ->view('emails.leave_request_notification')
                    ->with([
                        'leaveRequest' => $this->leaveRequest,
                        'status' => 'New'
                    ]);
    }
}
