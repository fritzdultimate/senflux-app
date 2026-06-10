{{-- resources/views/emails/verify-email.blade.php --}}

<x-emails.layout
    title="Verify your email — Senflux"
    preheader="Confirm your email address to activate your Senflux account.">



    {{-- ── Heading ── --}}
    <tr>
        <td style="padding:0 40px 10px;" class="px-mobile">
            <h1 style="margin:0;font-size:22px;font-weight:800;color:#ffffff;line-height:1.2;">
                Verify your email address
            </h1>
        </td>
    </tr>

    {{-- ── Body text ── --}}
    <tr>
        <td style="padding:0 40px 28px;" class="px-mobile">
            <p style="margin:0 0 14px;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Hi {{ $user->firstname ?? $user->name }},
            </p>
            <p style="margin:0 0 14px;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Thanks for creating your Senflux account. Click the button below to verify
                your email and unlock full access to live market intelligence, signals, and your dashboard.
            </p>
            <p style="margin:0;font-size:14px;color:#7a7a9a;line-height:1.7;">
                This link expires in <span style="color:#c8c8e0;font-weight:600;">60 minutes</span>.
            </p>
        </td>
    </tr>

    {{-- ── CTA button ── --}}
    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="border-radius:10px;
                                background:linear-gradient(135deg,#7B5CF5,#4F46E5);
                                box-shadow:0 4px 18px rgba(123,92,245,.45);">
                        <a href="{{ $url }}"
                           class="btn"
                           style="display:inline-block;padding:14px 32px;
                                  font-size:14px;font-weight:700;color:#ffffff;
                                  text-decoration:none;letter-spacing:.03em;
                                  border-radius:10px;mso-padding-alt:0;">
                            Verify email address
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- ── Fallback URL ── --}}
    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="background-color:rgba(255,255,255,.03);border-radius:10px;
                          border:1px solid rgba(255,255,255,.07);">
                <tr>
                    <td style="padding:16px;">
                        <p style="margin:0 0 6px;font-size:11.5px;color:#4a4a6a;text-transform:uppercase;
                                  letter-spacing:.08em;font-weight:600;">
                            Or copy this link
                        </p>
                        <p style="margin:0;font-size:12px;color:#6b6b8a;word-break:break-all;line-height:1.5;">
                            {{ $url }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- ── Security note ── --}}
    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="background-color:rgba(16,185,129,.06);border-radius:10px;
                          border:1px solid rgba(16,185,129,.15);">
                <tr>
                    <td style="padding:14px 16px;">
                        <p style="margin:0;font-size:12.5px;color:#7a7a9a;line-height:1.6;">
                            <span style="color:#10B981;font-weight:600;">Security note:</span>
                            If you didn't create a Senflux account, you can safely ignore this email.
                            No action is needed.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</x-emails.layout>