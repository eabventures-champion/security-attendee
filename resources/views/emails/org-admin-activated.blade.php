<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Approved & Activated - AttendFlow</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 900;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #a7f3d0;
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
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 16px 0;
            border-radius: 14px;
            font-weight: 900;
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
                <p>ORGANIZATION WORKSPACE ACTIVATED</p>
                <h1>Account Approved!</h1>
            </div>
            
            <div class="content">
                <div class="greeting">Hello {{ $orgAdmin->name }},</div>
                <div class="message">
                    Great news! Your <strong>AttendFlow Organization Workspace</strong> has been officially reviewed, approved, and activated by Super Admin.
                </div>

                <div class="card">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td class="card-label">Admin Email</td>
                            <td class="card-value">{{ $orgAdmin->email }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Account Role</td>
                            <td class="card-value">Organization Admin</td>
                        </tr>
                        <tr>
                            <td class="card-label">Workspace Access Status</td>
                            <td class="card-value" style="color: #34d399;">✅ Approved & Active</td>
                        </tr>
                    </table>
                </div>

                <p style="font-size: 13px; color: #a7f3d0; text-align: center; margin-top: 16px; font-weight: 600;">
                    Please click the button below to confirm receipt of your workspace approval and activate your access.
                </p>

                <a href="{{ $confirmUrl }}" class="button">Confirm Receipt & Accept Workspace</a>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} AttendFlow SaaS Platform. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
