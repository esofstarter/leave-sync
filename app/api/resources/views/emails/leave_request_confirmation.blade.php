<!DOCTYPE html>
<html>
<head>
    <title>APPROVED</title>
</head>
<body>
@php
    use Carbon\Carbon;
    $formattedStartDate = Carbon::parse($leaveRequest->start_date)->format('F j, Y');
    $formattedEndDate = $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('F j, Y') : null;
@endphp
    <h1>Leave Request Approved</h1>
    @if($leaveRequest->end_date == null)
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} leave on {{ $formattedStartDate }} </h2>
    @else
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} {{ $leaveRequest->days }} days leave from {{ $formattedStartDate }} to {{ $formattedEndDate }} </h2>
    @endif
    <a href="{{ url('http://165.232.82.246/admin/leave_request/' . $leaveRequest->id) }}" target="_blank">
        View Leave Request
    </a>
</body>
</html>