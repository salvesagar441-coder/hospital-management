<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: view_patients.php"); exit(); }

$id      = (int)($_POST['id'] ?? 0);
$name    = trim($_POST['name'] ?? '');
$age     = (int)($_POST['age'] ?? 0);
$gender  = trim($_POST['gender'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$blood_group = trim($_POST['blood_group'] ?? 'None');
$height  = !empty($_POST['height']) ? (int)$_POST['height'] : null;
$weight  = !empty($_POST['weight']) ? (int)$_POST['weight'] : null;
$date    = trim($_POST['date'] ?? '');
$time    = trim($_POST['time'] ?? '');
$status  = trim($_POST['status'] ?? 'active');

if (!$id || empty($name)) { $_SESSION['error'] = 'Invalid data.'; header("Location: view_patients.php"); exit(); }

$stmt = $pdo->prepare("UPDATE patients SET name=?, age=?, gender=?, phone=?, address=?, blood_group=?, height=?, weight=?, date=?, time=?, status=? WHERE id=?");
$stmt->execute([$name, $age, $gender, $phone, $address, $blood_group, $height, $weight, $date, $time, $status, $id]);

$_SESSION['success'] = "Patient updated successfully!";
header("Location: view_patients.php");
exit();
?>
