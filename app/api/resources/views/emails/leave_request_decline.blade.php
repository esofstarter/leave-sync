<!DOCTYPE html>
<html>
<head>
    <title>DECLINED</title>
</head>
<body>
    <h2>Your Leave Request Has Been Declined</h2>
        <a href="{{ url('/admin/leave_request/' . $leaveRequest->id) }}" target="_blank">
            Open Leave Request
        </a>
</body>
</html>