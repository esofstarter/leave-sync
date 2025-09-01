<!DOCTYPE html>
<html>
<head>
    <title>APPROVED UPDATED</title>
</head>
<body>
@php
    use Carbon\Carbon;
    $formattedStartDate = Carbon::parse($leaveRequest->start_date)->format('F j, Y');
    $formattedEndDate = $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('F j, Y') : null;
@endphp
    <h1>Leave Request Approved Updated</h1>
    @if($leaveRequest->end_date == null)
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} leave on {{ $formattedStartDate }} </h2>
    @else
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} {{ $leaveRequest->days }} days leave from {{ $formattedStartDate }} to {{ $formattedEndDate }} </h2>
    @endif
    <a href="{{ url('/admin/leave_request/' . $leaveRequest->id) }}" target="_blank">
        View Leave Request
    </a>
</body>
</html>