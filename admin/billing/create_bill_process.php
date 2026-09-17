<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header("Location: create_bill.php");exit();}
$patient_id  = (int)($_POST['patient_id'] ?? 0);
$doctor_fee  = (int)($_POST['doctor_fee'] ?? 0);
$medicine_fee= (int)($_POST['medicine_fee'] ?? 0);
$room_fee    = (int)($_POST['room_fee'] ?? 0);
$other_fee   = (int)($_POST['other_fee'] ?? 0);
$status      = $_POST['status'] === 'paid' ? 'paid' : 'unpaid';
$total       = $doctor_fee + $medicine_fee + $room_fee + $other_fee;
if(!$patient_id){$_SESSION['error']='Please select a patient.';header("Location: create_bill.php");exit();}
$stmt = $pdo->prepare("INSERT INTO billing (patient_id, doctor_fee, medicine_fee, room_fee, other_fee, total_amount, status) VALUES (?,?,?,?,?,?,?)");
$stmt->execute([$patient_id,$doctor_fee,$medicine_fee,$room_fee,$other_fee,$total,$status]);
$_SESSION['success'] = "Bill created successfully! Total: ₹".number_format($total);
header("Location: view_bill.php");exit();
