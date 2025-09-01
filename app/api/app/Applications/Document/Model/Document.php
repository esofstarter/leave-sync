<?php

namespace App\Applications\Document\Model;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['user_id', 'leave_request_id', 'file_name', 'file_path'];
    public $timestamps = true;
}
