<?php
session_start();
session_destroy();
header("Location: /hospital-management-system/auth/login.php");
exit();
?>
