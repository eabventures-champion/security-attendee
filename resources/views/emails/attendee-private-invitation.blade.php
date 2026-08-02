<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You are Invited!</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #ddd6fe;
            font-size: 14px;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
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
        .pass-box {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
            border: 1px solid #6d28d9;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin-bottom: 24px;
        }
        .pass-title {
            font-size: 11px;
            font-weight: 800;
            color: #c4b5fd;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .pass-token {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #a78bfa;
            word-break: break-all;
        }

        /* Countdown & Progress Bar Styling */
        .countdown-card {
            background: linear-gradient(135deg, #0f172a 0%, #2e1065 100%);
            border: 1px solid #6d28d9;
            border-radius: 18px;
            padding: 24px 20px;
            margin-top: 24px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.3);
        }
        .countdown-header-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .countdown-title {
            font-size: 11px;
            font-weight: 800;
            color: #c4b5fd;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .countdown-status {
            font-size: 10px;
            font-weight: 800;
            background-color: rgba(167, 139, 250, 0.2);
            color: #ddd6fe;
            padding: 3px 10px;
            border-radius: 9999px;
            border: 1px solid rgba(167, 139, 250, 0.3);
            text-align: right;
        }
        .time-box {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 8px;
        }
        .time-unit {
            font-size: 24px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 4px;
            font-family: 'Courier New', monospace;
        }
        .time-label {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }
        .progress-bar-bg {
            width: 100%;
            height: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #7c3aed 0%, #a78bfa 50%, #34d399 100%);
            border-radius: 9999px;
        }
        .progress-labels-table {
            width: 100%;
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            margin-top: 6px;
        }

        .button {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 16px 0;
            border-radius: 14px;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 10px 20px -3px rgba(124, 58, 237, 0.4);
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
    @php
        $now = \Illuminate\Support\Carbon::now();
        $startsAt = $event->starts_at ? \Illuminate\Support\Carbon::parse($event->starts_at) : null;
        $createdAt = $event->created_at ? \Illuminate\Support\Carbon::parse($event->created_at) : $now->copy()->subDays(7);

        $daysLeft = 0;
        $hoursLeft = 0;
        $minsLeft = 0;
        $progressPercent = 0;

        if ($startsAt) {
            if ($now->greaterThanOrEqualTo($startsAt)) {
                $progressPercent = 100;
            } else {
                $totalDuration = max(1, $createdAt->diffInSeconds($startsAt));
                $elapsed = max(0, $createdAt->diffInSeconds($now));
                $progressPercent = min(95, max(12, round(($elapsed / $totalDuration) * 100)));

                $diff = $now->diff($startsAt);
                $daysLeft = $diff->d + ($diff->m * 30);
                $hoursLeft = $diff->h;
                $minsLeft = $diff->i;
            }
        }
    @endphp

    <div class="wrapper">
        <div class="container">
            <div class="header">
                <p>Exclusive Invitation</p>
                <h1>{{ $event->name }}</h1>
            </div>
            
            <div class="content">
                <div class="greeting">Hello {{ $attendee->full_name }},</div>
                <div class="message">
                    You have received a personal invitation to attend <strong style="color: #ffffff;">{{ $event->name }}</strong>. Your unique entry pass has been pre-generated with security verification.
                </div>

                <div class="card">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <tr>
                            <td class="card-label">Event</td>
                            <td class="card-value">{{ $event->name }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Date & Time</td>
                            <td class="card-value">{{ $event->starts_at ? $event->starts_at->format('F j, Y @ g:i A') : 'Date TBA' }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Venue</td>
                            <td class="card-value">{{ $event->venue_name ?? 'Location TBA' }}</td>
                        </tr>
                        <tr>
                            <td class="card-label">Access Role</td>
                            <td class="card-value">{{ is_object($attendee->access_role) ? $attendee->access_role->label() : ucfirst($attendee->access_role) }}</td>
                        </tr>
                    </table>
                </div>

                @if($attendee->qrCode)
                    <div class="pass-box">
                        <div class="pass-title">Your Unique Pass Token</div>
                        <div class="pass-token">{{ $attendee->qrCode->secure_token }}</div>
                    </div>
                @endif

                <!-- Event Timeline Countdown Progress Bar Card -->
                <div class="countdown-card">
                    <table class="countdown-header-table" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="countdown-title">EVENT TIMELINE & COUNTDOWN</td>
                            <td align="right">
                                <span class="countdown-status">
                                    @if($startsAt && $now->greaterThanOrEqualTo($startsAt))
                                        IN PROGRESS
                                    @else
                                        COUNTDOWN RUNNING
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>

                    @if($startsAt && $now->lessThan($startsAt))
                        <!-- Countdown Units -->
                        <table width="100%" border="0" cellspacing="6" cellpadding="0">
                            <tr>
                                <td align="center" width="33%">
                                    <div class="time-box">
                                        <div class="time-unit">{{ sprintf('%02d', $daysLeft) }}</div>
                                        <div class="time-label">DAYS</div>
                                    </div>
                                </td>
                                <td align="center" width="33%">
                                    <div class="time-box">
                                        <div class="time-unit">{{ sprintf('%02d', $hoursLeft) }}</div>
                                        <div class="time-label">HOURS</div>
                                    </div>
                                </td>
                                <td align="center" width="33%">
                                    <div class="time-box">
                                        <div class="time-unit">{{ sprintf('%02d', $minsLeft) }}</div>
                                        <div class="time-label">MINUTES</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    @else
                        <div style="text-align: center; color: #34d399; font-weight: 700; font-size: 15px; padding: 12px 0;">
                            🚀 Event is Live & Underway!
                        </div>
                    @endif

                    <!-- Date Progress Bar -->
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <table class="progress-labels-table" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>Invitation Issued</td>
                            <td align="right">{{ $startsAt ? $startsAt->format('M j, Y') : 'Event Day' }}</td>
                        </tr>
                    </table>
                </div>

                <a href="{{ $inviteUrl }}" class="button">Access & Confirm Pass</a>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} AttendFlow. All rights reserved. <br>
                This invitation link and pass token are non-transferable.
            </div>
        </div>
    </div>
</body>
</html>
