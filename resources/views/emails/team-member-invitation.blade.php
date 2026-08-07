<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team Invitation - AttendFlow</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .container {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #e0e7ff;
            font-size: 13px;
            margin: 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
        }
        .message {
            font-size: 14px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 24px;
        }
        .card {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .card-label {
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }
        .card-value {
            color: #f1f5f9;
            font-weight: 600;
            text-align: right;
        }
        .role-badge {
            background-color: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .pass-box {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
            border: 1px solid #f59e0b;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            margin-bottom: 24px;
        }
        .pass-title {
            font-size: 11px;
            font-weight: 800;
            color: #fbbf24;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .pass-token {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 800;
            color: #fef3c7;
            letter-spacing: 1px;
        }
        .button {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 16px 0;
            border-radius: 14px;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.4);
            margin-top: 24px;
        }
        .footer {
            padding: 24px 30px;
            background-color: #0f172a;
            border-top: 1px solid #1e293b;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p>Team Member Access Invitation</p>
                <h1>Welcome to {{ $organizationName }}</h1>
            </div>
            
            <div class="content">
                <div class="greeting">Hello {{ $user->name }},</div>
                <div class="message">
                    You have been added as a team member on <strong>{{ $organizationName }}</strong>. Your administrator has assigned you the role of <strong class="role-badge">{{ $roleLabel }}</strong>.
                </div>

                <div class="card">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td class="card-label">Email Address</td>
                            <td class="card-value">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Assigned Role</td>
                            <td class="card-value">{{ $roleLabel }}</td>
                        </tr>
                        @if($user->assignedGate)
                            <tr>
                                <td class="card-label">Assigned Entry Gate</td>
                                <td class="card-value" style="color: #fbbf24;">🔒 {{ $user->assignedGate->name }}{{ $user->assignedGate->event ? ' ('.$user->assignedGate->event->name.')' : '' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="card-label">Invitation Status</td>
                            <td class="card-value" style="color: #60a5fa;">Pending Receipt Confirmation</td>
                        </tr>
                    </table>
                </div>

                @if(!empty($rawPassword))
                    <div class="pass-box">
                        <div class="pass-title">Your Assigned Login Password</div>
                        <div class="pass-token">{{ $rawPassword }}</div>
                    </div>
                @endif

                <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 10px;">
                    Click the button below to confirm your team membership, verify your account, and sign in directly to your dashboard.
                </p>

                <a href="{{ $inviteUrl }}" class="button">Accept Invitation & Access Dashboard</a>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} AttendFlow. All rights reserved.<br>
                This invitation link is unique and secure to {{ $user->email }}.
            </div>
        </div>
    </div>
</body>
</html>
