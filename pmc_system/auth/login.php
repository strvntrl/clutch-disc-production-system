<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PMC System</title>
    <link rel="icon" type="image/png" href="assets/img/logo_pmc.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2a5a, #1c3d7a);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .login-container h2 {
            margin-bottom: 25px;
            color: #0f2a5a;
            font-size: 26px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .input-group label {
            font-size: 13px;
            color: #333;
            margin-bottom: 6px;
            display: block;
            font-weight: 600;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }

        .input-group input:focus {
            border-color: #0f2a5a;
            box-shadow: 0 0 5px rgba(15, 42, 90, 0.3);
        }

        .login-btn {
            width: 100%;
            padding: 13px;
            background: #0f2a5a;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #163d85;
        }

        .error-box {
            background: #fdecea;
            color: #b71c1c;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: left;
        }

        .back-link {
            display: inline-block;
            margin-top: 18px;
            font-size: 13px;
            color: #0f2a5a;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="subtitle">Production Management Control</div>
        <h2>Login PMC</h2>

        <?php if ($error === '1'): ?>
            <div class="error-box">Username atau password salah.</div>
        <?php elseif ($error === '2'): ?>
            <div class="error-box">Akun ini tidak aktif. Hubungi administrator.</div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus autocomplete="username">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <button class="login-btn" type="submit">Login</button>
        </form>

        <a class="back-link" href="../../view/drilling.php">&larr; Kembali ke Dashboard Monitoring</a>
    </div>
</body>

</html>