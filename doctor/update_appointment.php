<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
require_once '../../config/db.php';

$id     = (int)($_GET['id'] ?? 0);
$status = in_array($_GET['status'] ?? '', ['completed','cancelled','pending']) ? $_GET['status'] : null;

if (!$id || !$status) {
    header("Location: /hospital-management-system/doctor/my_appointments.php");
    exit();
}

// Make sure this appointment belongs to this doctor
$stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
$stmt->execute([$status, $id, $_SESSION['user_id']]);

$_SESSION['success'] = "Appointment marked as $status.";
header("Location: /hospital-management-system/doctor/my_appointments.php");
exit();
?>
