<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VIP Early Access Invitation</title>
</head>
<body style="font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #020617; color: #f8fafc; margin: 0; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        
        <!-- Header / Logo -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-block; width: 48px; height: 48px; background: linear-gradient(135deg, #2563eb, #7c3aed); border-radius: 14px; font-size: 24px; font-weight: 800; color: #ffffff; line-height: 48px; text-align: center;">
                AF
            </div>
            <h2 style="color: #ffffff; margin-top: 15px; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0;">AttendFlow VIP Access</h2>
        </div>

        <!-- VIP Badge -->
        <div style="text-align: center; margin-bottom: 25px;">
            <span style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; padding: 6px 16px; border-radius: 100px; display: inline-block;">
                ⭐ Exclusive Launch Perks Unlocked
            </span>
        </div>

        <h1 style="color: #ffffff; font-size: 22px; font-weight: 800; text-align: center; margin-bottom: 20px; line-height: 1.3;">
            You're Invited to Claim Your 50% Off Lifetime VIP Discount!
        </h1>

        <p style="color: #94a3b8; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
            Hello,
        </p>

        <p style="color: #cbd5e1; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
            {{ $customMessage ?: "Thank you for joining the AttendFlow VIP Early Access waitlist! We are excited to announce that our premium event attendance management platform and subscription packages are now officially open." }}
        </p>

        <!-- Promo Code Box -->
        <div style="background: rgba(30, 41, 59, 0.8); border: 1px dashed #f59e0b; border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 30px;">
            <div style="color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                Your Exclusive VIP Promo Code
            </div>
            <div style="color: #fbbf24; font-size: 28px; font-weight: 900; letter-spacing: 3px; font-family: monospace;">
                {{ $promoCode }}
            </div>
            <div style="color: #38bdf8; font-size: 13px; margin-top: 8px; font-weight: 600;">
                Valid for 50% Off Any Subscription Package
            </div>
        </div>

        <!-- CTA Button -->
        <div style="text-align: center; margin-bottom: 35px;">
            <a href="{{ url('/register') }}" style="background: linear-gradient(135deg, #2563eb, #7c3aed); color: #ffffff; font-size: 15px; font-weight: 800; text-decoration: none; padding: 14px 32px; border-radius: 12px; display: inline-block; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);">
                Claim VIP Access &amp; Get Started →
            </a>
        </div>

        <p style="color: #64748b; font-size: 13px; line-height: 1.5; text-align: center; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px; margin: 0;">
            Need assistance or custom enterprise onboarding? Reply directly to this email or visit <a href="{{ url('/') }}" style="color: #38bdf8; text-decoration: none;">AttendFlow</a>.
        </p>
    </div>
</body>
</html>
