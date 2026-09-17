<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Create Bill — LifeCare HMS";
$patients = $pdo->query("SELECT id, name FROM patients WHERE is_deleted=0 AND status='active' ORDER BY name")->fetchAll();
$prePatient = (int)($_GET['patient_id'] ?? 0);
$error = $_SESSION['error'] ?? ''; unset($_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>
<div class="topbar">
    <div class="topbar-left"><h1>Create Bill</h1><div class="breadcrumb">Dashboard / Billing / Create</div></div>
    <div class="topbar-right"><a href="view_bill.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back</a></div>
</div>
<div class="content-area">
<?php if($error): ?><div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card" style="max-width:700px;margin:0 auto;">
    <div class="card-header"><h3><i class="fa fa-file-invoice-dollar" style="color:var(--success);margin-right:7px"></i>New Bill</h3></div>
    <div class="card-body">
        <form action="create_bill_process.php" method="POST" id="billForm">
            <div class="form-group">
                <label class="form-label">Select Patient <span>*</span></label>
                <select name="patient_id" class="form-control" required>
                    <option value="">-- Choose Patient --</option>
                    <?php foreach($patients as $pt): ?>
                    <option value="<?= $pt['id'] ?>" <?= $prePatient==$pt['id']?'selected':'' ?>><?= htmlspecialchars($pt['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Doctor Consultation Fee (₹)</label>
                    <input type="number" name="doctor_fee" id="doctor_fee" class="form-control fee-input" value="500" min="0" onchange="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Medicine / Pharmacy (₹)</label>
                    <input type="number" name="medicine_fee" id="medicine_fee" class="form-control fee-input" value="0" min="0" onchange="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Room / Ward Charges (₹)</label>
                    <input type="number" name="room_fee" id="room_fee" class="form-control fee-input" value="0" min="0" onchange="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Other Charges (₹)</label>
                    <input type="number" name="other_fee" id="other_fee" class="form-control fee-input" value="0" min="0" onchange="calcTotal()">
                </div>
            </div>
            <div style="background:var(--primary-light);border-radius:10px;padding:16px 20px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:15px;font-weight:600;color:var(--primary-dark)">Total Amount</span>
                <span style="font-size:24px;font-weight:800;color:var(--primary)" id="totalDisplay">₹500</span>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Status</label>
                <select name="status" class="form-control">
                    <option value="unpaid">Unpaid</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border);">
                <a href="view_bill.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Bill</button>
            </div>
        </form>
    </div>
</div>
</div>
<script>
function calcTotal() {
    const ids = ['doctor_fee','medicine_fee','room_fee','other_fee'];
    const total = ids.reduce((s,id) => s + (parseInt(document.getElementById(id).value)||0), 0);
    document.getElementById('totalDisplay').textContent = '₹' + total.toLocaleString('en-IN');
}
calcTotal();
</script>
<?php include '../../admin/partials/footer.php'; ?>
