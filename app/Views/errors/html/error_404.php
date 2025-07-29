<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>404 - Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, 'Liberation Sans', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }

        .message {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
        }

        .home-btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 2rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.2s;
        }

        .home-btn:hover {
            background: #0056b3;
        }

        @media (max-width: 500px) {
            .container {
                padding: 2rem 1rem;
            }

            .error-code {
                font-size: 4rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="error-code">404</div>
        <div class="message">Oops! The page you are looking for does not exist.<br>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <small style="color:#888;display:block;margin-top:10px;">Error: <?= nl2br(esc($message)) ?></small>
            <?php endif; ?>
        </div>
        <a href="/" class="home-btn">Back to Home</a>
    </div>
</body>

</html>