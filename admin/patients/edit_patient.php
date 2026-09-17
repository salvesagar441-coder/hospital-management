<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Edit Patient — LifeCare HMS";

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: view_patients.php"); exit(); }

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Patient not found.'; header("Location: view_patients.php"); exit(); }

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Edit Patient</h1>
        <div class="breadcrumb">Dashboard / Patients / Edit</div>
    </div>
    <div class="topbar-right">
        <a href="view_patients.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="content-area">
    <?php if ($error): ?>
    <div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card" style="max-width:860px;margin:0 auto;">
        <div class="card-header">
            <h3><i class="fa fa-user-pen" style="color:var(--warning);margin-right:7px"></i>Edit: <?= htmlspecialchars($p['name']) ?></h3>
        </div>
        <div class="card-body">
            <form action="edit_patient_process.php" method="POST">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label class="form-label">Full Name <span>*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($p['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" value="<?= $p['age'] ?>" min="0" max="150">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="Male" <?= $p['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $p['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $p['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($p['phone']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <?php foreach (['None','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                            <option value="<?= $bg ?>" <?= $p['blood_group'] === $bg ? 'selected' : '' ?>><?= $bg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" name="height" class="form-control" value="<?= $p['height'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" name="weight" class="form-control" value="<?= $p['weight'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admission Date</label>
                        <input type="date" name="date" class="form-control" value="<?= $p['date'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admission Time</label>
                        <input type="time" name="time" class="form-control" value="<?= $p['time'] ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="discharged" <?= $p['status'] === 'discharged' ? 'selected' : '' ?>>Discharged</option>
                            <option value="critical" <?= $p['status'] === 'critical' ? 'selected' : '' ?>>Critical</option>
                        </select>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($p['address']) ?></textarea>
                    </div>
                </div>
                <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;padding-top:20px;border-top:1px solid var(--border);">
                    <a href="view_patients.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../admin/partials/footer.php'; ?>
