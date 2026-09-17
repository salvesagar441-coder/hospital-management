<?php
// Redirect to print for now (PDF generation requires additional library)
$id = (int)($_GET['id'] ?? 0);
header("Location: print_bill.php?id=$id");
exit();
