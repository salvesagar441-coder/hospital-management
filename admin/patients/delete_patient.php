<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: view_patients.php"); exit(); }

$stmt = $pdo->prepare("UPDATE patients SET is_deleted = 1, status = 'deleted' WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Patient moved to bin.";
header("Location: view_patients.php");
exit();
?>
