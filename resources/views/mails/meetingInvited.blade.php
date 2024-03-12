<!DOCTYPE html>
<html lang="en">
<head>
    <title>You have a new meeting invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>You have a new meeting invitation</h1>
    <p>
        You have been invited to a new meeting. Please click the button below to join the meeting.
    </p>
    <a href="{{ $url }}" class="button">Join meeting</a>
    <p>
        If you did not expect to receive this email, please ignore it.
    </p>
</div>
</body>
</html>
