<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Senflux</title>
</head>

<body style="font-family:Arial,sans-serif;background:#080812;color:#fff;padding:40px">

    <div style="max-width:650px;margin:auto;background:#111120;border:1px solid #26263b;border-radius:16px;padding:32px">

        <h1 style="margin-top:0">
            Thanks for reaching out.
        </h1>

        <p style="color:#9B7DFF">
            We received your message.
        </p>

        <p style="line-height:1.7;color:#aaaac0">
            Hi {{ $submission->name }}, thanks for contacting Senflux.
            Your message has been received and a member of our team will
            review it and get back to you as soon as possible.
        </p>

        <div style="margin-top:28px;padding:18px;background:#090914;border-radius:10px">
            <strong>Your subject</strong>
            <div style="margin-top:7px;color:#aaaac0">
                {{ $submission->subject }}
            </div>
        </div>

        <p style="margin-top:30px;color:#77778f">
            Senflux
        </p>

    </div>

</body>
</html>
