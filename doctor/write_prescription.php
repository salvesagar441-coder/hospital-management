<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
require_once('../config/db.php');
$pageTitle = "Write Prescription — LifeCare HMS";

$appointment_id = (int)($_GET['appointment_id'] ?? 0);
if (!$appointment_id) {
    header("Location: /hospital-management-system/doctor/my_appointments.php");
    exit();
}

// Get appointment + patient details
$stmt = $pdo->prepare("
    SELECT a.*, p.name as patient_name, p.age, p.gender, p.phone, p.blood_group, p.address
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->execute([$appointment_id, $_SESSION['user_id']]);
$appt = $stmt->fetch();

if (!$appt) {
    $_SESSION['error'] = 'Appointment not found.';
    header("Location: /hospital-management-system/doctor/my_appointments.php");
    exit();
}

// Check if prescription already exists
$existing = $pdo->prepare("SELECT * FROM prescriptions WHERE appointment_id = ? AND doctor_id = ?");
$existing->execute([$appointment_id, $_SESSION['user_id']]);
$prescription = $existing->fetch();

$medicines = [];
if ($prescription) {
    $meds = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id = ?");
    $meds->execute([$prescription['id']]);
    $medicines = $meds->fetchAll();
}

$error   = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/hospital-management-system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .medicine-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1.5fr 40px;
            gap: 10px;
            align-items: center;
            padding: 10px;
            background: var(--surface2);
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid var(--border);
        }
        .medicine-row input, .medicine-row select {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 13px;
            font-family: inherit;
            background: var(--surface);
            outline: none;
            transition: border-color 0.2s;
        }
        .medicine-row input:focus, .medicine-row select:focus {
            border-color: var(--primary);
        }
        .remove-btn {
            width: 34px; height: 34px;
            background: var(--danger-light);
            color: var(--danger);
            border: 1px solid #FECACA;
            border-radius: 7px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
        }
        .remove-btn:hover { background: var(--danger); color: white; }
        .col-header {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1.5fr 40px;
            gap: 10px;
            padding: 0 10px;
            margin-bottom: 6px;
        }
        .col-header span {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-muted);
        }
        .patient-info-bar {
            background: var(--primary-light);
            border: 1px solid #BFDBFE;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .patient-info-bar .avatar {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: white;
            flex-shrink: 0;
        }
        .info-item { margin-right: 16px; }
        .info-item .lbl { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
        .info-item .val { font-size: 14px; font-weight: 700; color: var(--text); }
    </style>
</head>
<body>
<div class="layout">

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🏥</div>
        <h2>LifeCare HMS</h2>
        <span>Doctor Panel</span>
    </div>
    <div class="sidebar-section-title">Menu</div>
    <nav>
        <a href="/hospital-management-system/doctor/dashboard.php">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span> Dashboard
        </a>
        <a href="/hospital-management-system/doctor/my_appointments.php" class="active">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span> My Appointments
        </a>
        <a href="/hospital-management-system/doctor/prescriptions.php">
            <span class="nav-icon"><i class="fa-solid fa-file-medical"></i></span> Prescriptions
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'D', 0, 1)) ?></div>
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                <div class="role">Doctor</div>
            </div>
        </div>
        <a href="/hospital-management-system/auth/logout.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:10px;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>

<div class="main-wrapper">
    <div class="topbar">
        <div class="topbar-left">
            <h1><?= $prescription ? 'Edit' : 'Write' ?> Prescription</h1>
            <div class="breadcrumb">Doctor Panel / Appointments / Prescription</div>
        </div>
        <div class="topbar-right">
            <?php if ($prescription): ?>
            <a href="/hospital-management-system/doctor/print_prescription.php?id=<?= $prescription['id'] ?>" class="btn btn-outline btn-sm" target="_blank">
                <i class="fa fa-print"></i> Print
            </a>
            <?php endif; ?>
            <a href="/hospital-management-system/doctor/my_appointments.php" class="btn btn-outline btn-sm">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="content-area">
        <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Patient Info Bar -->
        <div class="patient-info-bar">
            <div class="avatar"><?= strtoupper(substr($appt['patient_name'] ?? 'P', 0, 1)) ?></div>
            <div class="info-item">
                <div class="lbl">Patient</div>
                <div class="val"><?= htmlspecialchars($appt['patient_name']) ?></div>
            </div>
            <div class="info-item">
                <div class="lbl">Age / Gender</div>
                <div class="val"><?= $appt['age'] ?>y / <?= htmlspecialchars($appt['gender']) ?></div>
            </div>
            <div class="info-item">
                <div class="lbl">Blood Group</div>
                <div class="val"><?= ($appt['blood_group'] && $appt['blood_group'] !== 'None') ? htmlspecialchars($appt['blood_group']) : '—' ?></div>
            </div>
            <div class="info-item">
                <div class="lbl">Phone</div>
                <div class="val"><?= htmlspecialchars($appt['phone']) ?></div>
            </div>
            <div class="info-item">
                <div class="lbl">Appt Date</div>
                <div class="val"><?= $appt['date'] ? date('d M Y', strtotime($appt['date'])) : '—' ?></div>
            </div>
        </div>

        <form action="/hospital-management-system/doctor/save_prescription.php" method="POST" id="prescForm">
            <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
            <input type="hidden" name="patient_id" value="<?= $appt['patient_id'] ?>">
            <?php if ($prescription): ?>
            <input type="hidden" name="prescription_id" value="<?= $prescription['id'] ?>">
            <?php endif; ?>

            <!-- Doctor Notes -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <h3><i class="fa fa-stethoscope" style="color:var(--primary);margin-right:7px"></i>Doctor Notes / Diagnosis</h3>
                </div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Diagnosis / Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Enter diagnosis, symptoms, and general notes..."><?= htmlspecialchars($prescription['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Medicines -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-pills" style="color:var(--success);margin-right:7px"></i>Medicines / Prescription</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addMedicine()">
                        <i class="fa fa-plus"></i> Add Medicine
                    </button>
                </div>
                <div class="card-body">
                    <div class="col-header">
                        <span>Medicine Name *</span>
                        <span>Dosage</span>
                        <span>Frequency</span>
                        <span>Duration</span>
                        <span>Instructions</span>
                        <span></span>
                    </div>

                    <div id="medicinesContainer">
                        <?php if (!empty($medicines)): ?>
                            <?php foreach ($medicines as $med): ?>
                            <div class="medicine-row">
                                <input type="text" name="medicine_name[]" placeholder="e.g. Paracetamol 500mg" value="<?= htmlspecialchars($med['medicine_name']) ?>" required>
                                <input type="text" name="dosage[]" placeholder="e.g. 1 tablet" value="<?= htmlspecialchars($med['dosage'] ?? '') ?>">
                                <select name="frequency[]">
                                    <option value="">Select</option>
                                    <?php
                                    $freqs = ['Once daily','Twice daily','Thrice daily','Four times daily','Every 8 hours','Every 6 hours','At bedtime','As needed'];
                                    foreach ($freqs as $f) {
                                        $sel = ($med['frequency'] === $f) ? 'selected' : '';
                                        echo "<option value='$f' $sel>$f</option>";
                                    }
                                    ?>
                                </select>
                                <input type="text" name="duration[]" placeholder="e.g. 5 days" value="<?= htmlspecialchars($med['duration'] ?? '') ?>">
                                <input type="text" name="instructions[]" placeholder="e.g. After meals" value="<?= htmlspecialchars($med['instructions'] ?? '') ?>">
                                <button type="button" class="remove-btn" onclick="removeMedicine(this)"><i class="fa fa-xmark"></i></button>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="medicine-row" id="firstRow">
                                <input type="text" name="medicine_name[]" placeholder="e.g. Paracetamol 500mg" required>
                                <input type="text" name="dosage[]" placeholder="e.g. 1 tablet">
                                <select name="frequency[]">
                                    <option value="">Select frequency</option>
                                    <option>Once daily</option>
                                    <option>Twice daily</option>
                                    <option>Thrice daily</option>
                                    <option>Four times daily</option>
                                    <option>Every 8 hours</option>
                                    <option>Every 6 hours</option>
                                    <option>At bedtime</option>
                                    <option>As needed</option>
                                </select>
                                <input type="text" name="duration[]" placeholder="e.g. 5 days">
                                <input type="text" name="instructions[]" placeholder="e.g. After meals">
                                <button type="button" class="remove-btn" onclick="removeMedicine(this)"><i class="fa fa-xmark"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                        <a href="/hospital-management-system/doctor/my_appointments.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> <?= $prescription ? 'Update' : 'Save' ?> Prescription
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function addMedicine() {
    const container = document.getElementById('medicinesContainer');
    const row = document.createElement('div');
    row.className = 'medicine-row';
    row.innerHTML = `
        <input type="text" name="medicine_name[]" placeholder="e.g. Paracetamol 500mg" required>
        <input type="text" name="dosage[]" placeholder="e.g. 1 tablet">
        <select name="frequency[]">
            <option value="">Select frequency</option>
            <option>Once daily</option>
            <option>Twice daily</option>
            <option>Thrice daily</option>
            <option>Four times daily</option>
            <option>Every 8 hours</option>
            <option>Every 6 hours</option>
            <option>At bedtime</option>
            <option>As needed</option>
        </select>
        <input type="text" name="duration[]" placeholder="e.g. 5 days">
        <input type="text" name="instructions[]" placeholder="e.g. After meals">
        <button type="button" class="remove-btn" onclick="removeMedicine(this)"><i class="fa fa-xmark"></i></button>
    `;
    container.appendChild(row);
}

function removeMedicine(btn) {
    const rows = document.querySelectorAll('.medicine-row');
    if (rows.length === 1) {
        alert('At least one medicine is required!');
        return;
    }
    btn.closest('.medicine-row').remove();
}

document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
});
</script>
</body>
</html>
