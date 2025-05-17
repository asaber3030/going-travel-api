<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>{{ $data['subject'] }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      color: #333;
      padding: 20px;
    }

    .email-container {
      border: 1px solid #e2e2e2;
      border-radius: 8px;
      padding: 20px;
      max-width: 600px;
      margin: auto;
      background-color: #f9f9f9;
    }

    h2 {
      color: #0056b3;
    }

    .info {
      margin-bottom: 10px;
    }

    .message {
      margin-top: 20px;
      white-space: pre-line;
    }
  </style>
</head>

<body>
  <div class="email-container">
    <h2>New Contact Message</h2>

    <div class="info"><strong>Name:</strong> {{ $data['name'] }}</div>
    <div class="info"><strong>Email:</strong> {{ $data['email'] }}</div>
    <div class="info"><strong>Subject:</strong> {{ $data['subject'] }}</div>

    <div class="message">
      <strong>Message:</strong><br>
      {{ $data['message'] }}
    </div>
  </div>
</body>

</html>