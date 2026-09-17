<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
require_once('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /hospital-management-system/doctor/my_appointments.php");
    exit();
}

$appointment_id  = (int)($_POST['appointment_id'] ?? 0);
$patient_id      = (int)($_POST['patient_id'] ?? 0);
$prescription_id = (int)($_POST['prescription_id'] ?? 0);
$notes           = trim($_POST['notes'] ?? '');
$doctor_id       = $_SESSION['user_id'];

$medicine_names  = $_POST['medicine_name'] ?? [];
$dosages         = $_POST['dosage'] ?? [];
$frequencies     = $_POST['frequency'] ?? [];
$durations       = $_POST['duration'] ?? [];
$instructions    = $_POST['instructions'] ?? [];

if (!$appointment_id || !$patient_id) {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: /hospital-management-system/doctor/my_appointments.php");
    exit();
}

try {
    $pdo->beginTransaction();

    if ($prescription_id) {
        // Update existing prescription
        $stmt = $pdo->prepare("UPDATE prescriptions SET notes = ? WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$notes, $prescription_id, $doctor_id]);
        // Delete old medicine items
        $pdo->prepare("DELETE FROM prescription_items WHERE prescription_id = ?")->execute([$prescription_id]);
    } else {
        // Create new prescription
        $stmt = $pdo->prepare("INSERT INTO prescriptions (appointment_id, patient_id, doctor_id, notes, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$appointment_id, $patient_id, $doctor_id, $notes]);
        $prescription_id = $pdo->lastInsertId();
    }

    // Insert medicine items
    $medStmt = $pdo->prepare("INSERT INTO prescription_items (prescription_id, medicine_name, dosage, frequency, duration, instructions) VALUES (?,?,?,?,?,?)");

    foreach ($medicine_names as $i => $med_name) {
        $med_name = trim($med_name);
        if (empty($med_name)) continue;
        $medStmt->execute([
            $prescription_id,
            $med_name,
            trim($dosages[$i] ?? ''),
            trim($frequencies[$i] ?? ''),
            trim($durations[$i] ?? ''),
            trim($instructions[$i] ?? ''),
        ]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Prescription saved successfully!";
    header("Location: /hospital-management-system/doctor/write_prescription.php?appointment_id=$appointment_id");

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error saving prescription: " . $e->getMessage();
    header("Location: /hospital-management-system/doctor/write_prescription.php?appointment_id=$appointment_id");
}
exit();
?>
