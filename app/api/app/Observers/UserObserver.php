<?php
namespace App\Observers;

use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\User\Model\User;

class UserObserver
{
    public function deleting(User $user): void
    {
        if (! $user->isForceDeleting()) {
            $user->leaveRequests()->delete();
        } else {
            $user->leaveRequests()->withTrashed()->forceDelete();
        }

        LeaveRequest::where('request_to', $user->id)->update(['request_to' => null]);
        LeaveRequest::where('confirmed_by', $user->id)->update(['confirmed_by' => null]);

        $user->clearMediaCollection('avatars');
    }
}
