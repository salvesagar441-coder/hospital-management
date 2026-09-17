<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$id     = (int)($_GET['id'] ?? 0);
$status = in_array($_GET['status'],['confirmed','cancelled','pending']) ? $_GET['status'] : 'pending';
if(!$id){header("Location: view_appointments.php");exit();}
$stmt = $pdo->prepare("UPDATE appointments SET status=? WHERE id=?");
$stmt->execute([$status,$id]);
$_SESSION['success'] = "Appointment ".ucfirst($status).".";
header("Location: view_appointments.php");exit();
