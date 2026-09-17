<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add_patient.php");
    exit();
}

$name        = trim($_POST['name'] ?? '');
$age         = (int)($_POST['age'] ?? 0);
$gender      = trim($_POST['gender'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$address     = trim($_POST['address'] ?? '');
$blood_group = trim($_POST['blood_group'] ?? 'None');
$height      = !empty($_POST['height']) ? (int)$_POST['height'] : null;
$weight      = !empty($_POST['weight']) ? (int)$_POST['weight'] : null;
$date        = trim($_POST['date'] ?? date('Y-m-d'));
$time        = trim($_POST['time'] ?? date('H:i'));

if (empty($name) || empty($gender) || empty($phone) || empty($address)) {
    $_SESSION['error'] = 'Please fill all required fields.';
    header("Location: add_patient.php");
    exit();
}

$stmt = $pdo->prepare("
    INSERT INTO patients (name, age, gender, phone, address, blood_group, height, weight, date, time, status, is_deleted)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 0)
");
$stmt->execute([$name, $age, $gender, $phone, $address, $blood_group, $height, $weight, $date, $time]);

$_SESSION['success'] = "Patient \"$name\" added successfully!";
header("Location: view_patients.php");
exit();
?>
