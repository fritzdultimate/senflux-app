<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Senflux Contact Submission</title>
</head>

<body style="font-family:Arial,sans-serif;background:#080812;color:#fff;padding:40px">

    <div style="max-width:650px;margin:auto;background:#111120;border:1px solid #26263b;border-radius:16px;padding:32px">

        <h1 style="margin-top:0;color:#9B7DFF">
            New Contact Submission
        </h1>

        <p>
            A new message has been submitted through the Senflux website.
        </p>

        <hr style="border:0;border-top:1px solid #29293d;margin:25px 0">

        <p><strong>Name:</strong> {{ $submission->name }}</p>

        <p><strong>Email:</strong> {{ $submission->email }}</p>

        @if($submission->company)
            <p><strong>Company:</strong> {{ $submission->company }}</p>
        @endif

        <p><strong>Subject:</strong> {{ $submission->subject }}</p>

        <p><strong>Message:</strong></p>

        <div style="background:#090914;border-radius:10px;padding:20px;line-height:1.7">
            {!! nl2br(e($submission->message)) !!}
        </div>

    </div>

</body>
</html>
