<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header("Location: create_appointment.php");exit();}
$patient_id = (int)($_POST['patient_id'] ?? 0);
$doctor_id  = (int)($_POST['doctor_id'] ?? 0);
$date       = trim($_POST['date'] ?? '');
$status     = in_array($_POST['status'],['pending','confirmed']) ? $_POST['status'] : 'pending';
if(!$patient_id||!$doctor_id||!$date){$_SESSION['error']='All fields are required.';header("Location: create_appointment.php");exit();}
$stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, date, status) VALUES (?,?,?,?)");
$stmt->execute([$patient_id,$doctor_id,$date,$status]);
$_SESSION['success'] = "Appointment booked successfully!";
header("Location: view_appointments.php");exit();
