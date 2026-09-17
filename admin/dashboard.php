<?php
require_once '../admin/middleware/auth_check.php';
require_once '../config/db.php';

$pageTitle = "Dashboard — LifeCare HMS";

// Stats
$totalPatients     = $pdo->query("SELECT COUNT(*) FROM patients WHERE is_deleted = 0")->fetchColumn();
$activePatients    = $pdo->query("SELECT COUNT(*) FROM patients WHERE status = 'active' AND is_deleted = 0")->fetchColumn();
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppt       = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$totalBilling      = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM billing")->fetchColumn();
$unpaidBills       = $pdo->query("SELECT COUNT(*) FROM billing WHERE status = 'unpaid'")->fetchColumn();
$totalDoctors      = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Doctor'")->fetchColumn();

// Recent patients
$recentPatients = $pdo->query("SELECT * FROM patients WHERE is_deleted = 0 ORDER BY id DESC LIMIT 5")->fetchAll();

// Recent billing
$recentBilling = $pdo->query("
    SELECT b.*, p.name as patient_name 
    FROM billing b 
    LEFT JOIN patients p ON b.patient_id = p.id 
    ORDER BY b.id DESC LIMIT 5
")->fetchAll();
?>
<?php include '../admin/partials/header.php'; ?>
<?php include '../admin/partials/sidebar.php'; ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Dashboard</h1>
        <div class="breadcrumb">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</div>
    </div>
    <div class="topbar-right">
        <div style="font-size:13px;color:var(--text-muted);"><?= date('D, d M Y') ?></div>
        <a href="/hospital-management-system/admin/patients/add_patient.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Patient
        </a>
    </div>
</div>

<div class="content-area">
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-bed-pulse" style="color:var(--primary)"></i></div>
            <div class="stat-info">
                <div class="label">Total Patients</div>
                <div class="value"><?= $totalPatients ?></div>
                <div class="change"><i class="fa fa-circle-dot" style="color:var(--success)"></i> <?= $activePatients ?> active</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fa-solid fa-calendar-check" style="color:var(--accent)"></i></div>
            <div class="stat-info">
                <div class="label">Appointments</div>
                <div class="value"><?= $totalAppointments ?></div>
                <div class="change" style="color:var(--warning)"><?= $pendingAppt ?> pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-indian-rupee-sign" style="color:var(--success)"></i></div>
            <div class="stat-info">
                <div class="label">Total Revenue</div>
                <div class="value">₹<?= number_format($totalBilling) ?></div>
                <div class="change" style="color:var(--danger)"><?= $unpaidBills ?> unpaid</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fa-solid fa-user-doctor" style="color:var(--secondary)"></i></div>
            <div class="stat-info">
                <div class="label">Doctors</div>
                <div class="value"><?= $totalDoctors ?></div>
                <div class="change">On staff</div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <!-- Recent Patients -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-bed-pulse" style="color:var(--primary);margin-right:6px"></i>Recent Patients</h3>
                <a href="/hospital-management-system/admin/patients/view_patients.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Age/Gender</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPatients as $p): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px;">
                                    <div class="patient-avatar"><?= strtoupper(substr($p['name'], 0, 1)) ?></div>
                                    <div>
                                        <div style="font-weight:600"><?= htmlspecialchars($p['name']) ?></div>
                                        <div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($p['phone']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= $p['age'] ?>y / <?= htmlspecialchars($p['gender']) ?></td>
                            <td>
                                <span class="badge <?= $p['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentPatients)): ?>
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:28px">No patients found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Billing -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-file-invoice-dollar" style="color:var(--success);margin-right:6px"></i>Recent Bills</h3>
                <a href="/hospital-management-system/admin/billing/view_bill.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBilling as $b): ?>
                        <tr>
                            <td style="font-weight:600"><?= htmlspecialchars($b['patient_name'] ?? 'N/A') ?></td>
                            <td style="font-weight:700;color:var(--primary)">₹<?= number_format($b['total_amount']) ?></td>
                            <td>
                                <span class="badge <?= $b['status'] === 'paid' ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ucfirst($b['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentBilling)): ?>
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:28px">No billing records</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include '../admin/partials/footer.php'; ?>
