<!DOCTYPE html>
<html>
<head>
    <title>500 - Server Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f5f5f5;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e74c3c;
            font-size: 48px;
            margin: 0;
        }
        p {
            color: #666;
            font-size: 18px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>500</h1>
        <p>Server Error</p>
        <p>{{ $message ?? 'An error occurred. Please try again later.' }}</p>
        <p><a href="/">Go to Homepage</a></p>
    </div>
</body>
</html>

