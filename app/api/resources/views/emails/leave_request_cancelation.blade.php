<!DOCTYPE html>
<html>
<head>
    <title>CANCELED</title>
</head>
<body>
@php
    use Carbon\Carbon;
    $formattedStartDate = Carbon::parse($leaveRequest->start_date)->format('F j, Y');
    $formattedEndDate = $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('F j, Y') : null;
@endphp
    <h1>Leave Request Canceled</h1>
    @if($leaveRequest->end_date == null)
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} leave on {{ $formattedStartDate }} </h2>
    @else
    <h2>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }} is on {{ $leaveRequest->leaveType->name }} leave from {{ $formattedStartDate }} to {{ $formattedEndDate }} </h2>
    @endif
    <h2>
        Days: {{ $leaveRequest->days }}
    </h2>
</body>
</html>