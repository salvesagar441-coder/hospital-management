<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
require_once '../../config/db.php';
$pageTitle = "My Prescriptions — LifeCare HMS";

$doctor_id = $_SESSION['user_id'];

$rxs = $pdo->prepare("
    SELECT pr.*, 
           p.name as patient_name, p.age, p.gender, p.phone,
           a.date as appt_date,
           (SELECT COUNT(*) FROM prescription_items pi WHERE pi.prescription_id = pr.id) as med_count
    FROM prescriptions pr
    LEFT JOIN patients p ON pr.patient_id = p.id
    LEFT JOIN appointments a ON pr.appointment_id = a.id
    WHERE pr.doctor_id = ?
    ORDER BY pr.created_at DESC
");
$rxs->execute([$doctor_id]);
$prescriptions = $rxs->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/hospital-management-system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <a href="/hospital-management-system/doctor/my_appointments.php">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span> My Appointments
        </a>
        <a href="/hospital-management-system/doctor/prescriptions.php" class="active">
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
            <h1>My Prescriptions</h1>
            <div class="breadcrumb">Doctor Panel / Prescriptions</div>
        </div>
    </div>

    <div class="content-area">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-file-medical" style="color:var(--success);margin-right:7px"></i>All Prescriptions <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($prescriptions) ?>)</span></h3>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Age/Gender</th>
                            <th>Appointment Date</th>
                            <th>Medicines</th>
                            <th>Issued On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $i => $rx): ?>
                        <tr>
                            <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    <div class="patient-avatar"><?= strtoupper(substr($rx['patient_name'] ?? 'P', 0, 1)) ?></div>
                                    <div>
                                        <div style="font-weight:600"><?= htmlspecialchars($rx['patient_name'] ?? 'N/A') ?></div>
                                        <div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($rx['phone'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $rx['age'] ?>y / <?= htmlspecialchars($rx['gender'] ?? '') ?></td>
                            <td style="color:var(--text-muted)"><?= $rx['appt_date'] ? date('d M Y', strtotime($rx['appt_date'])) : '—' ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fa fa-pills"></i> <?= $rx['med_count'] ?> medicine<?= $rx['med_count'] != 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td style="color:var(--text-muted)"><?= $rx['created_at'] ? date('d M Y', strtotime($rx['created_at'])) : '—' ?></td>
                            <td>
                                <div style="display:flex;gap:5px;">
                                    <a href="/hospital-management-system/doctor/print_prescription.php?id=<?= $rx['id'] ?>" class="btn btn-outline btn-icon btn-sm" title="Print" target="_blank">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <a href="/hospital-management-system/doctor/write_prescription.php?appointment_id=<?= $rx['appointment_id'] ?>" class="btn btn-warning btn-icon btn-sm" title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($prescriptions)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fa fa-file-medical" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3"></i>
                                No prescriptions written yet
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
