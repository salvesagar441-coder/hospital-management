<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Add Patient — LifeCare HMS";

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Add Patient</h1>
        <div class="breadcrumb">Dashboard / Patients / Add</div>
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
            <h3><i class="fa fa-user-plus" style="color:var(--primary);margin-right:7px"></i>Patient Information</h3>
        </div>
        <div class="card-body">
            <form action="add_patient_process.php" method="POST">
                <div class="form-grid">
                    <div class="form-group form-full">
                        <label class="form-label">Full Name <span>*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter patient full name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age <span>*</span></label>
                        <input type="number" name="age" class="form-control" placeholder="Age in years" min="0" max="150" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender <span>*</span></label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number <span>*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number" maxlength="15" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="None">Unknown / Not Tested</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" name="height" class="form-control" placeholder="Height in cm" min="0" max="300">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" name="weight" class="form-control" placeholder="Weight in kg" min="0" max="500">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admission Date <span>*</span></label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Admission Time <span>*</span></label>
                        <input type="time" name="time" class="form-control" value="<?= date('H:i') ?>" required>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-label">Address <span>*</span></label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Full address" required></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;padding-top:20px;border-top:1px solid var(--border);">
                    <a href="view_patients.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../admin/partials/footer.php'; ?>
