<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved & Activated - AttendFlow</title>
    <style>
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 10px !important; }
            .email-container { width: 100% !important; border-radius: 16px !important; }
            .email-header { padding: 28px 20px !important; }
            .email-content { padding: 24px 18px !important; }
            .email-title { font-size: 22px !important; }
            .card-table td { display: block !important; width: 100% !important; text-align: left !important; padding: 4px 0 !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#090d16; font-family:'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing:antialiased;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#090d16; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="email-wrapper" style="max-width: 580px; margin: 0 auto; padding: 0 12px;">
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="email-container" style="background-color:#1e293b; border:1px solid #334155; border-radius:24px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.5);">
                                <!-- Header -->
                                <tr>
                                    <td class="email-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%); padding: 36px 28px; text-align: center;">
                                        <p style="color:#a7f3d0; font-size:11px; margin:0 0 6px 0; font-weight:800; text-transform:uppercase; letter-spacing:1.5px;">ORGANIZATION WORKSPACE ACTIVATED</p>
                                        <h1 class="email-title" style="color:#ffffff; font-size:25px; font-weight:900; margin:0; letter-spacing:-0.5px;">Account Approved!</h1>
                                    </td>
                                </tr>
                                
                                <!-- Content -->
                                <tr>
                                    <td class="email-content" style="padding: 32px 28px;">
                                        <div style="font-size:18px; font-weight:800; color:#ffffff; margin-bottom:12px;">Hello {{ $orgAdmin->name }},</div>
                                        <div style="font-size:14px; line-height:1.6; color:#94a3b8; margin-bottom:24px;">
                                            Great news! Your <strong>AttendFlow Organization Workspace</strong> has been officially reviewed, approved, and activated by Super Admin.
                                        </div>

                                        <!-- Details Card -->
                                        <div style="background-color:#0f172a; border:1px solid #334155; border-radius:16px; padding:20px; margin-bottom:24px;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="6" class="card-table">
                                                <tr>
                                                    <td style="color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; font-size:11px;">Admin Email</td>
                                                    <td style="color:#f1f5f9; font-weight:700; font-size:13px; text-align:right;">{{ $orgAdmin->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; font-size:11px;">Account Role</td>
                                                    <td style="color:#f1f5f9; font-weight:700; font-size:13px; text-align:right;">Organization Admin</td>
                                                </tr>
                                                <tr>
                                                    <td style="color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; font-size:11px;">Workspace Access Status</td>
                                                    <td style="color:#34d399; font-weight:800; font-size:13px; text-align:right;">✅ Approved & Active</td>
                                                </tr>
                                            </table>
                                        </div>

                                        <p style="font-size:13px; color:#a7f3d0; text-align:center; margin: 0 0 20px 0; font-weight:600; line-height: 1.5;">
                                            Please click the button below to confirm receipt of your workspace approval and activate your access.
                                        </p>

                                        <!-- Button -->
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $confirmUrl }}" style="display:block; width:100%; box-sizing:border-box; background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); color:#ffffff !important; text-decoration:none; text-align:center; padding:16px 20px; border-radius:14px; font-weight:900; font-size:15px; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.4);">
                                                        Confirm Receipt & Accept Workspace
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Footer -->
                                <tr>
                                    <td style="padding:20px 28px; background-color:#0f172a; border-top:1px solid #1e293b; text-align:center; font-size:12px; color:#64748b;">
                                        &copy; {{ date('Y') }} AttendFlow SaaS Platform. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
