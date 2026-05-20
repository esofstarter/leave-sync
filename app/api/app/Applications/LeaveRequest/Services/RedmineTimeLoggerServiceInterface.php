<?php

namespace App\Applications\LeaveRequest\Services;

use App\Applications\LeaveRequest\Model\LeaveRequest;

interface RedmineTimeLoggerServiceInterface
{
    /**
     * Log time entries in Redmine for an approved leave request
     * 
     * @param LeaveRequest $leaveRequest
     * @return bool
     */
    public function logLeaveTimeEntries(LeaveRequest $leaveRequest): bool;

    /**
     * Delete time entries from Redmine for a leave request
     * 
     * @param LeaveRequest $leaveRequest
     * @return bool
     */
    public function deleteLeaveTimeEntries(LeaveRequest $leaveRequest): bool;
}
