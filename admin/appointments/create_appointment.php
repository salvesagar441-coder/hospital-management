<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Book Appointment — LifeCare HMS";
$patients = $pdo->query("SELECT id,name FROM patients WHERE is_deleted=0 AND status='active' ORDER BY name")->fetchAll();
$doctors  = $pdo->query("SELECT id,name,consultation_fee FROM users WHERE role='Doctor' ORDER BY name")->fetchAll();
$prePatient = (int)($_GET['patient_id'] ?? 0);
$error = $_SESSION['error'] ?? ''; unset($_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>
<div class="topbar">
    <div class="topbar-left"><h1>Book Appointment</h1><div class="breadcrumb">Dashboard / Appointments / Book</div></div>
    <div class="topbar-right"><a href="view_appointments.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back</a></div>
</div>
<div class="content-area">
<?php if($error): ?><div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card" style="max-width:600px;margin:0 auto;">
    <div class="card-header"><h3><i class="fa fa-calendar-plus" style="color:var(--primary);margin-right:7px"></i>New Appointment</h3></div>
    <div class="card-body">
        <form action="create_appointment_process.php" method="POST">
            <div class="form-group">
                <label class="form-label">Patient <span>*</span></label>
                <select name="patient_id" class="form-control" required>
                    <option value="">-- Select Patient --</option>
                    <?php foreach($patients as $pt): ?>
                    <option value="<?= $pt['id'] ?>" <?= $prePatient==$pt['id']?'selected':'' ?>><?= htmlspecialchars($pt['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Doctor <span>*</span></label>
                <select name="doctor_id" class="form-control" required>
                    <option value="">-- Select Doctor --</option>
                    <?php foreach($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> — ₹<?= number_format($d['consultation_fee']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Appointment Date & Time <span>*</span></label>
                <input type="datetime-local" name="date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                </select>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border);">
                <a href="view_appointments.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Book Appointment</button>
            </div>
        </form>
    </div>
</div>
</div>
<?php include '../../admin/partials/footer.php'; ?>
