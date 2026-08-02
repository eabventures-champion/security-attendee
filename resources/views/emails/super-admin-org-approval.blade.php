<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Approval Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #090d16;
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #d946ef 100%);
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 900;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #f0abfc;
            font-size: 12px;
            margin: 0;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .content {
            padding: 32px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 800;
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
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }
        .card-value {
            color: #f1f5f9;
            font-weight: 700;
            text-align: right;
        }
        .button {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 16px 0;
            border-radius: 14px;
            font-weight: 900;
            font-size: 15px;
            box-shadow: 0 10px 20px -3px rgba(16, 185, 129, 0.4);
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
                <p>SUPER ADMIN AUTHORIZATION SYSTEM</p>
                <h1>New Organization Registration</h1>
            </div>
            
            <div class="content">
                <div class="greeting">Hello Super Admin,</div>
                <div class="message">
                    A new organization has registered on <strong>AttendFlow</strong> and is awaiting your explicit Super Admin approval before their dashboard and workspace can be accessed.
                </div>

                <div class="card">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td class="card-label">Organization Name</td>
                            <td class="card-value" style="color: #60a5fa; font-size: 15px;">{{ $organization->name }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Organization Admin</td>
                            <td class="card-value">{{ $orgAdmin->name }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Admin Email</td>
                            <td class="card-value">{{ $orgAdmin->email }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Registered At</td>
                            <td class="card-value">{{ $orgAdmin->created_at ? $orgAdmin->created_at->format('F j, Y @ g:i A') : 'Just now' }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Approval Status</td>
                            <td class="card-value" style="color: #fbbf24;">⚠️ Pending Super Admin Confirmation</td>
                        </tr>
                    </table>
                </div>

                <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 10px;">
                    Review the organization details above and click below to approve and immediately activate their workspace.
                </p>

                <a href="{{ $approvalUrl }}" class="button">Approve & Activate Organization Workspace</a>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} AttendFlow SaaS Platform. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
