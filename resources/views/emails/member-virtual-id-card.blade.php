<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Official Virtual ID Card</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0b1329;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px 15px;
        }
        .container {
            background-color: #111d38;
            border: 1px solid #1e293b;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
            padding: 30px 24px;
            text-align: center;
            border-bottom: 1px solid #3730a3;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #c7d2fe;
            font-size: 13px;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 28px 24px;
        }
        /* ID Card Badge Styling */
        .id-card {
            background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
            border: 2px solid #3b82f6;
            border-radius: 20px;
            padding: 24px;
            margin: 20px 0;
            position: relative;
            box-shadow: 0 10px 30px -5px rgba(59, 130, 246, 0.2);
        }
        .card-top {
            border-bottom: 1px solid #334155;
            padding-bottom: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-size: 11px;
            font-weight: 800;
            color: #60a5fa;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .card-id-num {
            font-size: 12px;
            font-weight: 800;
            color: #93c5fd;
            font-family: monospace;
            margin: 0;
        }
        .card-body {
            display: flex;
            gap: 18px;
            align-items: center;
        }
        .photo-box {
            width: 90px;
            height: 110px;
            border-radius: 12px;
            border: 2px solid #60a5fa;
            background: #0f172a;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .member-info {
            flex: 1;
        }
        .member-name {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 6px 0;
        }
        .info-row {
            font-size: 12px;
            color: #94a3b8;
            margin: 3px 0;
        }
        .info-row strong {
            color: #e2e8f0;
        }
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #334155;
        }
        .qr-img {
            width: 130px;
            height: 130px;
            border-radius: 12px;
            border: 3px solid #ffffff;
            background: #ffffff;
            padding: 6px;
            display: inline-block;
        }
        .btn-primary {
            display: block;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            text-align: center;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            margin: 24px 0 12px 0;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }
        .footer {
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #1e293b;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ $card->organization ? $card->organization->name : config('app.name') }}</h1>
                <p>OFFICIAL VIRTUAL MEMBERSHIP ID CARD</p>
            </div>

            <div class="content">
                <div style="font-size: 16px; font-weight: 700; color: #ffffff; margin-bottom: 8px;">
                    Hello {{ $card->full_name }},
                </div>
                <div style="font-size: 13px; line-height: 1.5; color: #94a3b8; margin-bottom: 20px;">
                    Your official digital Virtual ID Card has been generated and activated. You can present this digital card or download it to your device for instant verification.
                </div>

                <!-- Virtual ID Card Visual -->
                <div class="id-card">
                    <div class="card-top">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($card->institution_logo_url)
                                <img src="{{ $card->institution_logo_url }}" alt="Logo" style="width: 26px; height: 26px; object-fit: contain; vertical-align: middle; border-radius: 6px;">
                            @endif
                            <div>
                                <p class="card-title" style="margin: 0; font-size: 11px; font-weight: 900; text-transform: uppercase; color: #60a5fa; line-height: 1.2;">Federation of African Law Students (FALAS)</p>
                                <p style="margin: 2px 0 0 0; font-size: 10px; font-weight: bold; color: #f97316;">{{ $card->institution ?: 'University of Ghana, School of Law' }}</p>
                            </div>
                        </div>
                        <p class="card-id-num">{{ $card->member_id_number }}</p>
                    </div>

                    <div class="card-body">
                        <div class="photo-box">
                            @if($card->photo_url)
                                <img src="{{ $card->photo_url }}" alt="Photo" class="photo-img">
                            @else
                                <svg width="50" height="50" fill="#64748b" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            @endif
                        </div>

                        <div class="member-info">
                            <h2 class="member-name">{{ $card->full_name }}</h2>
                            <div class="info-row"><strong>Institution / Faculty:</strong> {{ $card->institution ?: 'FALAS' }}</div>
                            @if($card->admission_year || $card->completion_year)
                                <div class="info-row">
                                    <strong>Period:</strong> {{ $card->admission_year ?: 'N/A' }} – {{ $card->completion_year ?: 'Present' }}
                                </div>
                            @endif
                            @if(!empty($card->custom_fields))
                                @foreach($card->custom_fields as $cfKey => $cfVal)
                                    @if(!empty($cfVal))
                                        <div class="info-row"><strong>{{ ucwords(str_replace('_', ' ', $cfKey)) }}:</strong> {{ $cfVal }}</div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="qr-section">
                        <img src="{{ $card->qr_code_url }}" alt="ID QR Verification" class="qr-img">
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 6px; font-family: monospace;">
                            SCAN TO VERIFY • TOKEN: {{ $card->qr_token }}
                        </div>
                    </div>
                </div>

                <!-- Access / View Online Button -->
                <a href="{{ $cardUrl }}" class="btn-primary">
                    View &amp; Download High-Res ID Card
                </a>
            </div>

            <div class="footer">
                <p style="margin: 0 0 4px 0;">Issued by {{ $card->organization ? $card->organization->name : config('app.name') }}</p>
                <p style="margin: 0;">This is an automated virtual credential dispatch. Please keep your digital card secure.</p>
            </div>
        </div>
    </div>
</body>
</html>
