<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email and password are required.';
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || $user['password'] !== $password) {
    $_SESSION['login_error'] = 'Invalid email or password.';
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}

$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['role']      = $user['role'];
$_SESSION['email']     = $user['email'];

// Role based redirect
if ($user['role'] === 'Doctor') {
    header("Location: /hospital-management-system/doctor/dashboard.php");
} else {
    header("Location: /hospital-management-system/admin/dashboard.php");
}
exit();
?>
