<?php

namespace App\Applications\NationalHoliday\Model;

use Illuminate\Database\Eloquent\Model;

class NationalHoliday extends Model
{
    protected $fillable = ['date', 'country', 'year'];
    public $timestamps = true;
}
