<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Base Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #07152bff; /* Light, neutral background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* 1. Login Container */
        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Constrain width for better focus */
        }
        
        /* 2. Header and Branding */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #777;
            font-size: 14px;
        }

        /* 3. Form Styling */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #007bff; /* Primary color focus */
            outline: none;
        }

        /* 4. Submission Button */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #007bff; /* Primary action color */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }

        /* 5. Error/Status Messages */
        .message-box {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h2>App Management System</h2>
            <p>Sign in to your account</p>
        </div>

        <?php 
        // Display error message from session if it exists
        if (isset($_SESSION['error'])): ?>
            <div class="message-box error">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']); 
                ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="form-group">
                <label for="username">Username / Email</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
            
            <p style="text-align: center; margin-top: 15px; font-size: 13px;">
                <a href="/forgot-password" style="color: #007bff; text-decoration: none;">Forgot Password?</a>
            </p>
        </form>
    </div>
</body>
</html>
