<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
        }
        @media only screen and (max-width: 640px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding: 24px 20px !important; }
            .email-title { font-size: 22px !important; line-height: 30px !important; }
            .email-button { display: block !important; text-align: center !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#F8FAFC; color:#0F172A; font-family:'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-text-size-adjust:100%;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">{{ $preheader }}</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#F8FAFC;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <!-- Main Email Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="email-shell" style="width:600px; max-width:600px; border-collapse:separate; background-color:#FFFFFF; border:2px solid #0F172A; box-shadow:4px 4px 0px #0F172A;">
                    <!-- Brand Top Header -->
                    <tr>
                        <td class="email-padding" style="padding:24px 32px; border-bottom:2px solid #0F172A; background-color:#FFFFFF;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td valign="middle" style="width:40px;">
                                        <div style="width:36px; height:36px; background-color:#1D4ED8; color:#FFFFFF; font-weight:900; font-size:22px; text-align:center; line-height:36px; border:1.5px solid #0F172A; box-shadow:2px 2px 0px #0F172A; font-family:'Merriweather', Georgia, serif;">
                                            N
                                        </div>
                                    </td>
                                    <td valign="middle" style="padding-left:12px;">
                                        <div style="font-size:20px; font-weight:800; letter-spacing:-0.5px; color:#1D4ED8; line-height:22px; font-family:'Be Vietnam Pro', sans-serif;">
                                            NewsHub
                                        </div>
                                        <div style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#64748B; margin-top:2px; font-family:'Be Vietnam Pro', sans-serif;">
                                            TÒA SOẠN ĐIỆN TỬ &amp; DIỄN ĐÀN ĐỘC GIẢ ĐA CHIỀU
                                        </div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span style="display:inline-block; border:1px solid #CBD5E1; background-color:#F8FAFC; padding:4px 10px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#475569; font-family:'Be Vietnam Pro', sans-serif;">
                                            Thông báo bảo mật
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="email-padding" style="padding:32px 32px 28px; background-color:#FFFFFF;">
                            <div style="display:inline-block; padding:4px 10px; border:1px solid #BFDBFE; background-color:#EFF6FF; color:#1D4ED8; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; font-family:'Be Vietnam Pro', sans-serif;">
                                {{ $eyebrow }}
                            </div>

                            <h1 class="email-title" style="margin:16px 0 0; color:#0F172A; font-size:24px; font-weight:800; line-height:32px; letter-spacing:-0.5px; font-family:'Be Vietnam Pro', -apple-system, sans-serif;">
                                {{ $title }}
                            </h1>

                            <p style="margin:18px 0 0; color:#0F172A; font-size:15px; line-height:24px; font-family:'Be Vietnam Pro', sans-serif;">
                                Xin chào <strong>{{ $recipientName }}</strong>,
                            </p>

                            <p style="margin:10px 0 0; color:#334155; font-size:15px; line-height:24px; font-family:'Be Vietnam Pro', sans-serif;">
                                {{ $intro }}
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:26px 0 0;">
                                <tr>
                                    <td>
                                        <a href="{{ $actionUrl }}" class="email-button" style="display:inline-block; padding:13px 28px; background-color:#1D4ED8; color:#FFFFFF !important; border:2px solid #0F172A; box-shadow:3px 3px 0px #0F172A; font-family:'Be Vietnam Pro', sans-serif; font-size:13px; font-weight:700; text-transform:uppercase; text-decoration:none; letter-spacing:0.5px;">
                                            {{ $actionLabel }} &nbsp;&rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0; color:#64748B; font-size:12px; line-height:18px; font-family:'Be Vietnam Pro', sans-serif;">
                                &bull; {{ $expiryText }}
                            </p>

                            <!-- Security Advisory Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:26px; background-color:#F8FAFC; border:1px solid #E2E8F0; border-left:4px solid #1D4ED8;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <div style="color:#1D4ED8; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; font-family:'Be Vietnam Pro', sans-serif;">
                                            &#128274; Lưu ý bảo mật
                                        </div>
                                        <div style="margin-top:5px; color:#475569; font-size:12.5px; line-height:20px; font-family:'Be Vietnam Pro', sans-serif;">
                                            {{ $securityText }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 6px; color:#64748B; font-size:11px; line-height:16px; font-family:'Be Vietnam Pro', sans-serif;">
                                Nếu nút trên không bấm được, bạn có thể sao chép liên kết sau dán vào thanh địa chỉ trình duyệt:
                            </p>
                            <p style="margin:0; word-break:break-all; font-size:11px; line-height:16px; font-family:'Be Vietnam Pro', sans-serif;">
                                <a href="{{ $actionUrl }}" style="color:#1D4ED8; text-decoration:underline;">{{ $actionUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-padding" style="padding:20px 32px; background-color:#F8FAFC; border-top:2px solid #0F172A; text-align:center;">
                            <p style="margin:0; color:#64748B; font-size:11px; line-height:18px; font-family:'Be Vietnam Pro', sans-serif;">
                                Email tự động được phát hành từ Tòa soạn NewsHub. Vui lòng không trả lời email này.
                            </p>
                            <p style="margin:6px 0 0; color:#0F172A; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; font-family:'Be Vietnam Pro', sans-serif;">
                                &copy; {{ now()->year }} NewsHub &bull; Báo điện tử đa phương tiện, Tin tức cập nhật 24/7.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
