{{-- resources/views/emails/kyc-approved.blade.php --}}

<x-emails.layout
    title="Identity verified — Senflux"
    preheader="Your {{ $submission->tier->label() }} identity verification has been approved.">

    <tr>
        <td style="padding:0 40px 10px;" class="px-mobile">
            <h1 style="margin:0;font-size:22px;font-weight:800;color:#ffffff;line-height:1.2;">
                You're verified
            </h1>
        </td>
    </tr>

    <tr>
        <td style="padding:0 40px 28px;" class="px-mobile">
            <p style="margin:0 0 14px;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Hi {{ $user->firstname ?? $user->name }},
            </p>
            <p style="margin:0 0 14px;font-size:14px;color:#7a7a9a;line-height:1.7;">
                Your <span style="color:#c8c8e0;font-weight:600;">{{ $submission->tier->label() }}</span>
                identity verification has been reviewed and approved.
            </p>
            <p style="margin:0;font-size:14px;color:#7a7a9a;line-height:1.7;">
                @if($submission->tier->value === 'basic')
                    You can now request withdrawals. Consider completing Enhanced verification to unlock higher limits.
                @else
                    Your account now has full Enhanced verification with the highest available withdrawal limits.
                @endif
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding:0 40px 32px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="border-radius:10px;
                                background:linear-gradient(135deg,#7B5CF5,#4F46E5);
                                box-shadow:0 4px 18px rgba(123,92,245,.45);">
                        <a href="{{ url(route('dashboard.kyc', absolute: false)) }}"
                           class="btn"
                           style="display:inline-block;padding:14px 32px;
                                  font-size:14px;font-weight:700;color:#ffffff;
                                  text-decoration:none;letter-spacing:.03em;
                                  border-radius:10px;">
                            View verification status
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</x-emails.layout>
