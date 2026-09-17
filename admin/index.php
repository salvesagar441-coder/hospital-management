<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /hospital-management-system/admin/dashboard.php");
} else {
    header("Location: /hospital-management-system/auth/login.php");
}
exit();
?>
