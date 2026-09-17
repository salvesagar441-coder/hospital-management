<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: bin.php"); exit(); }

$stmt = $pdo->prepare("UPDATE patients SET is_deleted = 0, status = 'active' WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Patient restored successfully!";
header("Location: bin.php");
exit();
?>
