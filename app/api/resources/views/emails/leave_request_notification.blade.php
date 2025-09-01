<!DOCTYPE html>
<html>
<head>
    <title>Leave Request: {{ $status }}</title>
</head>
<body>
@php
    use Carbon\Carbon;
    $formattedStartDate = Carbon::parse($leaveRequest->start_date)->format('F j, Y');
    $formattedEndDate = $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('F j, Y') : null;
@endphp
    <h1>{{ $status }}</h1>
    <h2>Leave Request Assigned to You</h2>
    <h3>{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }}</h3>
    @if ($leaveRequest->end_date == null)
    <h3>{{ $formattedStartDate }}</h3>
    @else
    <h3>{{ $formattedStartDate }} to {{ $formattedEndDate }}</h3>
    @endif

    <h3>Days: {{ $leaveRequest->days }}</h3>

    @if ($leaveRequest->reason && $leaveRequest->reason !== null)
        <h3>
            Reason: {{ $leaveRequest->reason ? $leaveRequest->reason : "" }}
        </h3>
    @endif

    <h3>{{ $leaveRequest->leaveType->name }}</h3>
        <a href="{{ url('http://165.232.82.246/admin/leave_request/' . $leaveRequest->id . '/confirmation') }}" target="_blank">
            Open Leave Request Confirmation Page
        </a>
</body>
</html>