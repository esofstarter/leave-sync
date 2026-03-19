<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Declined</title>
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
            background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
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
        .content {
            padding: 30px 20px;
        }
        .greeting {
            color: #d32f2f;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .message {
            background-color: #fef5f5;
            border-left: 4px solid #d32f2f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #666;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('/images/esof_logo.png') }}" alt="ESOF Logo" class="logo">
            <h1 class="header-title">Leave Request Declined</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Hello,</p>
            
            <div class="message">
                <p>Your leave request has been <strong>declined</strong>. Please contact your manager for more information.</p>
            </div>
            
            <p>You can review the details of your request and any feedback from your manager:</p>
            
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