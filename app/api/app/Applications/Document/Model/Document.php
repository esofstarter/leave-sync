<?php

namespace App\Applications\Document\Model;

use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\User\Model\User;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['user_id', 'leave_request_id', 'file_name', 'file_path'];
    public $timestamps = true;

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id')->withTrashed();
    }

}
