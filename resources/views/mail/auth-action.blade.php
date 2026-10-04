<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F7F6F0;
            color: #171715;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
        }
        @media only screen and (max-width: 640px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding: 24px 20px !important; }
            .email-title { font-size: 24px !important; line-height: 32px !important; }
            .email-button { display: block !important; text-align: center !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#F7F6F0; color:#171715; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-text-size-adjust:100%;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">{{ $preheader }}</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#F7F6F0;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <!-- Main Email Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="email-shell" style="width:600px; max-width:600px; border-collapse:separate; background-color:#FFFEFA; border:2px solid #171715; box-shadow:4px 4px 0px #171715;">
                    <!-- Brand Top Header -->
                    <tr>
                        <td class="email-padding" style="padding:28px 36px; border-bottom:2px solid #171715; background-color:#FFFEFA;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td valign="middle">
                                        <div style="font-size:22px; font-weight:900; letter-spacing:-0.5px; text-transform:uppercase; color:#171715;">
                                            NEWS<span style="background-color:#D4FF3F; border:1.5px solid #171715; padding:2px 6px; margin-left:2px; font-size:18px;">HUB</span>
                                        </div>
                                        <div style="font-family:'Courier New', Courier, monospace; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#66645E; margin-top:4px;">
                                            Tòa soạn Tin tức & Tri thức số
                                        </div>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span style="display:inline-block; border:1px solid #171715; background-color:#F7F6F0; padding:4px 8px; font-family:'Courier New', Courier, monospace; font-size:10px; font-weight:bold; text-transform:uppercase; color:#171715;">
                                            Thông báo bảo mật
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="email-padding" style="padding:36px 36px 32px; background-color:#FFFEFA;">
                            <div style="display:inline-block; padding:3px 8px; border:1px solid #171715; background-color:#D4FF3F; color:#171715; font-family:'Courier New', Courier, monospace; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.8px;">
                                {{ $eyebrow }}
                            </div>

                            <h1 class="email-title" style="margin:18px 0 0; color:#171715; font-size:30px; font-weight:900; line-height:36px; text-transform:uppercase; letter-spacing:-0.5px;">
                                {{ $title }}
                            </h1>

                            <p style="margin:20px 0 0; color:#171715; font-size:15px; line-height:24px;">
                                Xin chào <strong>{{ $recipientName }}</strong>,
                            </p>

                            <p style="margin:12px 0 0; color:#403F3B; font-size:15px; line-height:25px;">
                                {{ $intro }}
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0 0;">
                                <tr>
                                    <td>
                                        <a href="{{ $actionUrl }}" class="email-button" style="display:inline-block; padding:14px 28px; background-color:#D4FF3F; color:#171715; border:2px solid #171715; box-shadow:3px 3px 0px #171715; font-family:'Courier New', Courier, monospace; font-size:13px; font-weight:800; text-transform:uppercase; text-decoration:none; letter-spacing:0.5px;">
                                            {{ $actionLabel }} &nbsp;&rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0; color:#66645E; font-family:'Courier New', Courier, monospace; font-size:12px; line-height:18px;">
                                &bull; {{ $expiryText }}
                            </p>

                            <!-- Security Advisory Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:28px; background-color:#F7F6F0; border:1px solid #171715; border-left:4px solid #171715;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <div style="color:#171715; font-family:'Courier New', Courier, monospace; font-size:12px; font-weight:800; text-transform:uppercase;">
                                            &#128274; Lưu ý bảo mật
                                        </div>
                                        <div style="margin-top:6px; color:#403F3B; font-size:13px; line-height:20px;">
                                            {{ $securityText }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:26px 0 6px; color:#66645E; font-family:'Courier New', Courier, monospace; font-size:11px; line-height:16px;">
                                Nếu nút trên không bấm được, bạn có thể sao chép liên kết sau dán vào thanh địa chỉ trình duyệt:
                            </p>
                            <p style="margin:0; word-break:break-all; font-family:'Courier New', Courier, monospace; font-size:11px; line-height:16px;">
                                <a href="{{ $actionUrl }}" style="color:#171715; text-decoration:underline;">{{ $actionUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-padding" style="padding:22px 36px; background-color:#F7F6F0; border-top:2px solid #171715; text-align:center;">
                            <p style="margin:0; font-family:'Courier New', Courier, monospace; color:#66645E; font-size:11px; line-height:18px;">
                                Email tự động được phát hành từ Tòa soạn NewsHub. Vui lòng không trả lời email này.
                            </p>
                            <p style="margin:6px 0 0; font-family:'Courier New', Courier, monospace; color:#171715; font-size:10px; font-weight:bold; text-transform:uppercase;">
                                &copy; {{ now()->year }} NewsHub &bull; Báo chí chính luận & Bản tin cập nhật.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
