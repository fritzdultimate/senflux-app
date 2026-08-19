{{-- resources/views/emails/two-factor-disabled.blade.php --}}

<x-emails.layout
    title="Two-factor authentication disabled — Senflux"
    preheader="Two-factor authentication was just turned off for your account.">

    <tr>
        <td style="padding:0 40px 10px;" class="px-mobile">
            <h1 style="margin:0;font-size:22px;font-weight:800;color:#ffffff;line-height:1.2;">
                Two-factor authentication was turned off
            </h1>
        </td>
    </tr>

    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <p style="margin:0 0 14px;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Hi {{ $user->firstname ?? $user->name }},
            </p>
            <p style="margin:0;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Two-factor authentication was just disabled on your Senflux account. Your account is now protected
                by your password alone — we recommend re-enabling 2FA as soon as possible.
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="background-color:rgba(244,63,94,.06);border-radius:10px;
                          border:1px solid rgba(244,63,94,.15);">
                <tr>
                    <td style="padding:14px 16px;">
                        <p style="margin:0;font-size:12.5px;color:#7a7a9a;line-height:1.6;">
                            <span style="color:#F43F5E;font-weight:600;">Wasn't you?</span>
                            If you didn't make this change, secure your account immediately.
                            <a href="{{ config('app.url') }}/login"
                               style="color:#9B7DFF;text-decoration:none;font-weight:600;">
                                Secure your account →
                            </a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</x-emails.layout>
