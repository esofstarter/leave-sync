<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Approved - Accounting</title>
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
            background: linear-gradient(135deg, #73BD18 0%, #5fa314 100%);
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
        .header-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin-top: 8px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            color: #73BD18;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .message {
            background-color: #f0f8e8;
            border-left: 4px solid #73BD18;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .details-box {
            background-color: #f9f9f9;
            border-left: 4px solid #73BD18;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .detail-item {
            margin-bottom: 12px;
        }
        .detail-label {
            color: #73BD18;
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
            <h1 class="header-title">Leave Request Approved</h1>
            <p class="header-subtitle">For Internal Accounting Records</p>
        </div>
        
        <div class="content">
            <p class="greeting">Dear Accounting Team,</p>
            
            <div class="message">
                <p>A leave request has been approved. Please find the details and attached documentation below for your records.</p>
            </div>
            
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
            
            <p>The attached PDF contains the official leave request documentation for your records.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} ESOF. All rights reserved.</p>
        </div>
    </div>
</body>
</html>