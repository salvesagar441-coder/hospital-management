<?php
require_once dirname(__DIR__) . '/config/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
$pageTitle = "Doctor Dashboard — LifeCare HMS";

$doctor_id = $_SESSION['user_id'];

// Stats for this doctor
$myAppointments   = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ?");
$myAppointments->execute([$doctor_id]);
$myAppointments   = $myAppointments->fetchColumn();

$pendingAppt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'pending'");
$pendingAppt->execute([$doctor_id]);
$pendingAppt = $pendingAppt->fetchColumn();

$completedAppt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'completed'");
$completedAppt->execute([$doctor_id]);
$completedAppt = $completedAppt->fetchColumn();

// Recent appointments for this doctor
$appts = $pdo->prepare("
    SELECT a.*, p.name as patient_name, p.phone, p.age, p.gender
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.id
    WHERE a.doctor_id = ?
    ORDER BY a.date DESC
    LIMIT 10
");
$appts->execute([$doctor_id]);
$appts = $appts->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/hospital-management-system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🏥</div>
        <h2>LifeCare HMS</h2>
        <span>Doctor Panel</span>
    </div>

    <div class="sidebar-section-title">Menu</div>
    <nav>
        <a href="/hospital-management-system/doctor/dashboard.php" class="active">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
            Dashboard
        </a>
        <a href="/hospital-management-system/doctor/my_appointments.php">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span>
            My Appointments
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'D', 0, 1)) ?></div>
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Doctor') ?></div>
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
            <h1>Doctor Dashboard</h1>
            <div class="breadcrumb">Welcome, Dr. <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</div>
        </div>
        <div class="topbar-right">
            <div style="font-size:13px;color:var(--text-muted)"><?= date('D, d M Y') ?></div>
        </div>
    </div>

    <div class="content-area">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-calendar-check" style="color:var(--primary)"></i></div>
                <div class="stat-info">
                    <div class="label">Total Appointments</div>
                    <div class="value"><?= $myAppointments ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fa-solid fa-clock" style="color:var(--accent)"></i></div>
                <div class="stat-info">
                    <div class="label">Pending</div>
                    <div class="value"><?= $pendingAppt ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-circle-check" style="color:var(--success)"></i></div>
                <div class="stat-info">
                    <div class="label">Completed</div>
                    <div class="value"><?= $completedAppt ?></div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-calendar-check" style="color:var(--accent);margin-right:7px"></i>My Appointments</h3>
                <a href="/hospital-management-system/doctor/my_appointments.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Age/Gender</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appts as $i => $a): ?>
                        <tr>
                            <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    <div class="patient-avatar"><?= strtoupper(substr($a['patient_name'] ?? 'P', 0, 1)) ?></div>
                                    <div>
                                        <div style="font-weight:600"><?= htmlspecialchars($a['patient_name'] ?? 'N/A') ?></div>
                                        <div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($a['phone'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $a['age'] ?>y / <?= htmlspecialchars($a['gender'] ?? '') ?></td>
                            <td style="color:var(--text-muted)">
                                <?= $a['date'] ? date('d M Y', strtotime($a['date'])) : '—' ?><br>
                                <span style="font-size:12px"><?= $a['date'] ? date('h:i A', strtotime($a['date'])) : '' ?></span>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($a['status']) {
                                    'completed' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    default => 'badge-warning'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst($a['status']) ?></span>
                            </td>
                            <td>
                                <?php if ($a['status'] === 'pending'): ?>
                                <div style="display:flex;gap:5px;">
                                    <a href="/hospital-management-system/doctor/update_appointment.php?id=<?= $a['id'] ?>&status=completed"
                                       class="btn btn-success btn-sm" onclick="return confirm('Mark as completed?')">
                                        <i class="fa fa-check"></i> Complete
                                    </a>
                                    <a href="/hospital-management-system/doctor/update_appointment.php?id=<?= $a['id'] ?>&status=cancelled"
                                       class="btn btn-danger btn-sm" onclick="return confirm('Cancel?')">
                                        <i class="fa fa-xmark"></i>
                                    </a>
                                </div>
                                <?php else: ?>
                                <span style="color:var(--text-muted);font-size:13px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($appts)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <i class="fa fa-calendar" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3"></i>
                                No appointments assigned yet
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- end main-wrapper -->
</div><!-- end layout -->

<script>
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
});
</script>
</body>
</html>
