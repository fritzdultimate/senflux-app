{{-- resources/views/emails/kyc-rejected.blade.php --}}

<x-emails.layout
    title="Identity verification update — Senflux"
    preheader="Your {{ $submission->tier->label() }} verification needs another look.">

    <tr>
        <td style="padding:0 40px 10px;" class="px-mobile">
            <h1 style="margin:0;font-size:22px;font-weight:800;color:#ffffff;line-height:1.2;">
                We couldn't verify your documents
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
                identity verification submission could not be approved.
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding:0 40px 28px;" class="px-mobile">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="background-color:rgba(244,63,94,.06);border-radius:10px;
                          border:1px solid rgba(244,63,94,.15);">
                <tr>
                    <td style="padding:16px;">
                        <p style="margin:0 0 6px;font-size:11.5px;color:#F43F5E;text-transform:uppercase;
                                  letter-spacing:.08em;font-weight:600;">
                            Reason
                        </p>
                        <p style="margin:0;font-size:13px;color:#c8c8e0;line-height:1.6;">
                            {{ $submission->rejection_reason }}
                        </p>
                    </td>
                </tr>
            </table>
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
                            Resubmit documents
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</x-emails.layout>
