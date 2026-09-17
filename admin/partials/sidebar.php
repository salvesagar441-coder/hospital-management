<?php
$current = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

function isActive($file, $dir = '') {
    global $current, $currentDir;
    if ($dir && $currentDir === $dir) return 'active';
    if ($current === $file) return 'active';
    return '';
}
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🏥</div>
        <h2>LifeCare HMS</h2>
        <span>Hospital Management</span>
    </div>

    <div class="sidebar-section-title">Main Menu</div>
    <nav>
        <a href="/hospital-management-system/admin/dashboard.php" class="<?= isActive('dashboard.php') ?>">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
            Dashboard
        </a>
        <a href="/hospital-management-system/admin/patients/view_patients.php" class="<?= isActive('', 'patients') ?>">
            <span class="nav-icon"><i class="fa-solid fa-bed-pulse"></i></span>
            Patients
        </a>
        <a href="/hospital-management-system/admin/appointments/view_appointments.php" class="<?= isActive('', 'appointments') ?>">
            <span class="nav-icon"><i class="fa-solid fa-calendar-check"></i></span>
            Appointments
        </a>
        <a href="/hospital-management-system/admin/billing/view_bill.php" class="<?= isActive('', 'billing') ?>">
            <span class="nav-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            Billing
        </a>
        <a href="/hospital-management-system/admin/prescriptions.php" class="<?= isActive('prescriptions.php') ?>">
            <span class="nav-icon"><i class="fa-solid fa-file-medical"></i></span>
            Prescriptions
        </a>

        <div class="sidebar-section-title">Management</div>
        <a href="/hospital-management-system/admin/patients/bin.php" class="<?= isActive('bin.php') ?>">
            <span class="nav-icon"><i class="fa-solid fa-trash-can"></i></span>
            Patient Bin
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
                <div class="role"><?= htmlspecialchars($_SESSION['role'] ?? 'Admin') ?></div>
            </div>
        </div>
        <a href="/hospital-management-system/auth/logout.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:10px;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>
<div class="main-wrapper">
