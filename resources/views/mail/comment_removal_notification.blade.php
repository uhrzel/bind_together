<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 2px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 100px;
            height: auto;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .header .title {
            font-size: 20px;
            font-weight: bold;
            color: #007BFF;
        }

        h2 {
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 15px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        .note {
            color: #666;
        }

        .note strong {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <p>Dear {{ $commentOwnerName }},</p>

    <p>We hope this message finds you well. We want to inform you that your comment, titled
        "<strong>[{{ $commentTitle }}]</strong>", was recently reported by another user for violating our community
        guidelines.</p>

    <p>After careful review, we have determined that the comment does not adhere to our standards and policies.
        As a result, the comment will be removed from our platform.</p>

    <p>We encourage you to review our community guidelines to ensure that future comments comply with our rules.
        If you believe this decision was made in error, please feel free to contact us for further clarification.</p>

    <p>Thank you for your understanding and cooperation in maintaining a respectful community space.</p>

    <p>Best regards,</p>
    <p>{{ auth()->user()->email }}</p>
    <p>[Admin]</p>
</body>

</html>
