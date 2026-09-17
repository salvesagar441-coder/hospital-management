<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
// Auto-generate bill with default doctor fee for a patient
$id = (int)($_GET['patient_id'] ?? 0);
if(!$id){header("Location: view_patients.php");exit();}
$stmt = $pdo->prepare("INSERT INTO billing (patient_id, doctor_fee, medicine_fee, room_fee, other_fee, total_amount, status) VALUES (?,500,0,0,0,500,'unpaid')");
$stmt->execute([$id]);
$_SESSION['success'] = "Auto-bill created successfully!";
header("Location: /hospital-management-system/admin/billing/view_bill.php");exit();
