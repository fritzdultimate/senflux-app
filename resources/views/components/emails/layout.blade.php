@props(['title' => 'Senflux', 'preheader' => ''])
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ $title }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
        table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}
        img{-ms-interpolation-mode:bicubic;border:0;outline:none;text-decoration:none}
        body{margin:0!important;padding:0!important;background-color:#05050c}
        @media only screen and (max-width:600px){
            .email-container{width:100%!important}
            .btn{width:100%!important;text-align:center!important}
            .px-mobile{padding-left:24px!important;padding-right:24px!important}
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#05050c;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    @if($preheader)
    <div style="display:none;font-size:1px;color:#05050c;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        {{ $preheader }}&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>
    @endif

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color:#05050c;">
        <tr>
            <td style="padding:32px 16px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                       width="560" class="email-container"
                       style="margin:0 auto;background-color:#0d0d1a;border-radius:16px;border:1px solid rgba(255,255,255,.07);overflow:hidden;">

                    {{-- Top gradient bar --}}
                    <tr>
                        <td style="height:3px;background:linear-gradient(90deg,#7B5CF5,#9B7DFF,#4F46E5);font-size:0;line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- Header / Logo --}}
                    <tr>
                        <td style="padding:32px 40px 28px;" class="px-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="vertical-align:middle;padding-right:8px;">
                                        {{-- SVG logo as table cell background trick won't work in email --}}
                                        {{-- Use a simple text+color mark instead — reliable across all clients --}}
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width:56px;height:56px;border-radius:8px;
                                                            background:linear-gradient(135deg,#7B5CF5,#4F46E5);
                                                            text-align:center;vertical-align:middle;">
                                                    <img src="{{ asset('assets/img/logo-2.png') }}"
                                                        width="24" height="24" alt=""
                                                        style="display:inline-block;vertical-align:middle;" />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span style="font-size:15px;font-weight:700;color:#ffffff;letter-spacing:.1em;">
                                            SEN<span style="color:#9B7DFF;">FLUX</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Body content slot ── --}}
                    {{ $slot }}

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:0 40px 36px;" class="px-mobile">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="height:1px;background:rgba(255,255,255,.07);font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding-top:20px;font-size:12px;color:#4a4a6a;line-height:1.7;text-align:center;">
                                        <p style="margin:0 0 5px;">© {{ date('Y') }} Senflux &nbsp;·&nbsp;
                                            <a href="{{ config('app.url') }}" style="color:#6b6b8a;text-decoration:none;">senflux.io</a>
                                            &nbsp;·&nbsp;
                                            <a href="{{ config('app.url') }}/privacy" style="color:#6b6b8a;text-decoration:none;">Privacy</a>
                                            &nbsp;·&nbsp;
                                            <a href="{{ config('app.url') }}/terms" style="color:#6b6b8a;text-decoration:none;">Terms</a>
                                        </p>
                                        <p style="margin:0;color:#3a3a5a;font-size:11px;">
                                            You're receiving this because you have an account at Senflux.
                                        </p>
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