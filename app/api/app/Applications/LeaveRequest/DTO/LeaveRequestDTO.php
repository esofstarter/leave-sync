<?php

namespace App\Applications\LeaveRequest\DTO;

use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\LeaveType\Model\LeaveType;
use App\Applications\User\Model\User;
use Illuminate\Http\Request;

class LeaveRequestDTO
{
    public int $user_id;
    public int $leave_type_id;
    public string $start_date;
    public ?string $end_date;
    public ?string $reason;
    public int $request_to;
    public int $confirmed_by;
    public int $is_confirmed;
    public int $status;
    public int $days;
    public int $id;
    public ?User $user; 
    public ?LeaveType $leaveType;
    
    public ?User $requestToUser;

    public function __construct(
        int $user_id,
        int $leave_type_id,
        string $start_date,
        ?string $end_date,
        ?string $reason,
        int $request_to,
        int $confirmed_by = 0,
        int $is_confirmed = 0,
        int $status = 0,
        int $days = 0,
        int $id = 0,
        ?User $user = null,   
        ?LeaveType $leaveType = null,
        ?User $requestToUser = null
    ) {
        $this->user_id = $user_id;
        $this->leave_type_id = $leave_type_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->reason = $reason;
        $this->request_to = $request_to;
        $this->confirmed_by = $confirmed_by;
        $this->is_confirmed = $is_confirmed;
        $this->status = $status;
        $this->days = $days;
        $this->id = $id;
        $this->user = $user;
        $this->leaveType = $leaveType;
        $this->requestToUser = $requestToUser;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->input('user_id'),
            leave_type_id: $request->input('leave_type_id'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            reason: $request->input('reason', ''),
            request_to: $request->input('request_to'),
            confirmed_by: $request->input('confirmed_by', 0),
            is_confirmed: $request->input('is_confirmed', 0),
            status: $request->input('status', 0),
            days: $request->input('days', 0),
            id: $request->input('id', 0)
        );
    }

    public static function fromRequestForCreate(Request $request): self
    {
        return new self(
            user_id: $request->input('user_id'),
            leave_type_id: $request->input('leave_type_id'),
            start_date: $request->input('start_date'),
            end_date: $request->input('end_date'),
            reason: $request->input('reason'),
            request_to: $request->input('request_to'),
            confirmed_by: 0,
            is_confirmed: 0,
            status: 0,
            days: $request->input('days', 0),
            id: 0
            // Excluding User and LeaveType (default to null)
        );
    }

    public static function fromModel(LeaveRequest $leaveRequest): self
    {
        return new self(
            user_id: $leaveRequest->user_id,
            leave_type_id: $leaveRequest->leave_type_id,
            start_date: $leaveRequest->start_date,
            end_date: $leaveRequest->end_date,
            reason: $leaveRequest->reason,
            request_to: $leaveRequest->request_to,
            confirmed_by: $leaveRequest->confirmed_by,
            is_confirmed: $leaveRequest->is_confirmed,
            status: $leaveRequest->status,
            days: $leaveRequest->days,
            id: $leaveRequest->id,
            user: $leaveRequest->user, 
            leaveType: $leaveRequest->leaveType,
            requestToUser: $leaveRequest->requestToUser
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'request_to' => $this->request_to,
            'confirmed_by' => $this->confirmed_by,
            'is_confirmed' => $this->is_confirmed,
            'status' => $this->status,
            'days' => $this->days,
            'id' => $this->id,
        ];
    }

    public static function fromCollection(iterable $leaveRequests): array
    {
        return array_map(fn (LeaveRequest $leaveRequest) => self::fromModel($leaveRequest), $leaveRequests->all());
    }
}
