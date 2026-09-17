<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /hospital-management-system/admin/dashboard.php");
    exit();
}
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — LifeCare HMS</title>
    <link rel="stylesheet" href="/hospital-management-system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #0D1B2E; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-wrap { width: 100%; max-width: 420px; padding: 24px; }
        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo .icon { width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 14px; }
        .login-logo h1 { font-family: 'Instrument Serif', serif; font-size: 26px; color: #fff; }
        .login-logo p { font-size: 13px; color: rgba(255,255,255,0.45); margin-top: 4px; }
        .login-card { background: #fff; border-radius: 20px; padding: 36px; box-shadow: 0 25px 60px rgba(0,0,0,0.4); }
        .login-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .login-card .sub { font-size: 13px; color: var(--text-muted); margin-bottom: 28px; }
        .input-wrap { position: relative; }
        .input-wrap .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; }
        .input-wrap input { padding-left: 38px !important; }
        .btn-login { width: 100%; justify-content: center; padding: 12px; font-size: 14.5px; border-radius: 10px; margin-top: 6px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
        .divider { text-align: center; font-size: 12px; color: var(--text-muted); margin: 18px 0; }
        .creds-hint { background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 12px; font-size: 12px; color: var(--text-muted); line-height: 1.8; }
        .creds-hint strong { color: var(--text); }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="icon">🏥</div>
        <h1>LifeCare HMS</h1>
        <p>Hospital Management System</p>
    </div>
    <div class="login-card">
        <h2>Welcome back</h2>
        <p class="sub">Sign in to your account to continue</p>

        <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="/hospital-management-system/auth/login_process.php" method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-wrap">
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="email">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fa fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fa fa-right-to-bracket"></i> Sign In
            </button>
        </form>

        <div class="divider">Demo Credentials</div>
        <div class="creds-hint">
            <strong>Admin:</strong> admin@gmail.com / 1234<br>
            <strong>Doctor:</strong> doctor@gmail.com / 1234
        </div>
    </div>
</div>
</body>
</html>
