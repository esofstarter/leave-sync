<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Updated</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 15px;
        }
        .header-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .badge {
            display: inline-block;
            background-color: rgba(255,255,255,0.3);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            text-transform: uppercase;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            color: #2196F3;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .details-box {
            background-color: #f9f9f9;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-item {
            margin-bottom: 12px;
        }
        .detail-label {
            color: #2196F3;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            color: #333;
            font-size: 16px;
            margin-top: 4px;
        }
        .cta-button {
            display: inline-block;
            background-color: #73BD18;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin-top: 20px;
            text-align: center;
        }
        .cta-button:hover {
            background-color: #5fa314;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;
    $formattedStartDate = Carbon::parse($leaveRequest->start_date)->format('F j, Y');
    $formattedEndDate = $leaveRequest->end_date ? Carbon::parse($leaveRequest->end_date)->format('F j, Y') : null;
@endphp
    <div class="container">
        <div class="header">
            <img src="{{ url('/images/esof_logo.png') }}" alt="ESOF Logo" class="logo">
            <h1 class="header-title">Leave Request Updated</h1>
            <span class="badge">Approved</span>
        </div>
        
        <div class="content">
            <p class="greeting">Hello,</p>
            
            <p>An approved leave request has been updated with new information.</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <div class="detail-label">Employee</div>
                    <div class="detail-value">{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Leave Type</div>
                    <div class="detail-value">{{ $leaveRequest->leaveType->name }}</div>
                </div>
                
                @if($leaveRequest->end_date == null)
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ $formattedStartDate }}</div>
                    </div>
                @else
                    <div class="detail-item">
                        <div class="detail-label">Period</div>
                        <div class="detail-value">{{ $formattedStartDate }} to {{ $formattedEndDate }}</div>
                    </div>
                @endif
                
                <div class="detail-item">
                    <div class="detail-label">Duration</div>
                    <div class="detail-value">{{ $leaveRequest->days }} {{ $leaveRequest->days == 1 ? 'day' : 'days' }}</div>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <p>Please review the updated leave request details:</p>
            
            <center>
                <a href="{{ url('/admin/leave_request/' . $leaveRequest->id) }}" class="cta-button" target="_blank">
                    View Leave Request
                </a>
            </center>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ESOF. All rights reserved.</p>
        </div>
    </div>
</body>
</html>