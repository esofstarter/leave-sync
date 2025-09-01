<?php

namespace App\Applications\LeaveRequest\Requests;

use App\Http\Requests\ApiFormRequest;
use DateTime;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use App\Applications\NationalHoliday\Model\NationalHoliday;
use App\Applications\LeaveRequest\Model\LeaveRequest;
use App\Applications\Country\Model\Country;

class NewLeaveRequestRequest extends ApiFormRequest
{
 /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // We will handle this with middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'request_to' => 'required',
            'leave_type_id' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ];
    }

    /**
     * Apply custom validation after basic rule validation.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $start_date = $this->input('start_date');
            $end_date = $this->input('end_date') ?? $start_date; // If no end date, assume single-day leave

            if ($this->hasExistingLeaveRequest($user, $start_date, $end_date)) {
                $validator->errors()->add('duplicate_leave', 'You already have a leave request for this date range.');
            }

            if ($this->input('leave_type_id') == 3) {
                $leaveDays = $this->calculateDays($this->input('start_date'), $this->input('end_date'), $user);
                if ($leaveDays > $user->paid_leaves_left) {
                    $validator->errors()->add('leave_days', 'Requested leave days exceed available paid leave balance.');
                }
            }
        });
    }

    private function hasExistingLeaveRequest($user, $start_date, $end_date)
    {
        return LeaveRequest::where('user_id', $user->id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('start_date', [$start_date, $end_date])
                      ->orWhereBetween('end_date', [$start_date, $end_date])
                      ->orWhere(function ($query) use ($start_date, $end_date) {
                          $query->where('start_date', '<=', $start_date)
                                ->where('end_date', '>=', $end_date);
                      });
            })
            ->exists();
    }


    /**
     * Calculate leave days, excluding weekends and national holidays.
     */
    private function calculateDays($start_date, $end_date, $user)
    {
        $isSingleDay = $end_date === null;

        if ($isSingleDay) {
            return 1;
        }

        $startDate = new DateTime($start_date);
        $endDate = new DateTime($end_date);

        $country = Country::find($user->country);

        $nationalHolidays = NationalHoliday::whereYear('date', $startDate->format('Y'))
            ->where('country', $country->name)
            ->pluck('date')
            ->toArray();

        $leaveDays = iterator_count(
            new DatePeriod($startDate, new DateInterval('P1D'), $endDate->modify('+1 day'))
        );

        // Remove weekends and national holidays from leave count
        $leaveDays -= count(array_filter(
            iterator_to_array(new DatePeriod($startDate, new DateInterval('P1D'), $endDate->modify('+1 day'))),
            fn($date) => in_array($date->format('N'), [6, 7]) || in_array($date->format('Y-m-d'), $nationalHolidays)
        ));

        return $leaveDays;
    }


    public function messages(){
        return [
            'leave_days' => 'Requested leave days exceed available paid leave balance.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to the start date.',
        ];
    }
}
