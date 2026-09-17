<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$id     = (int)($_GET['id'] ?? 0);
$status = $_GET['status'] === 'paid' ? 'paid' : 'unpaid';
if(!$id){header("Location: view_bill.php");exit();}
$stmt = $pdo->prepare("UPDATE billing SET status=? WHERE id=?");
$stmt->execute([$status,$id]);
$_SESSION['success'] = "Bill marked as $status.";
header("Location: view_bill.php");exit();
