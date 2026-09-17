<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Billing — LifeCare HMS";

$viewId = (int)($_GET['id'] ?? 0);
if ($viewId) {
    $stmt = $pdo->prepare("SELECT b.*, p.name as patient_name, p.phone, p.address, p.age, p.gender FROM billing b LEFT JOIN patients p ON b.patient_id = p.id WHERE b.id = ?");
    $stmt->execute([$viewId]); $bill = $stmt->fetch();
}
$bills = $pdo->query("SELECT b.*, p.name as patient_name FROM billing b LEFT JOIN patients p ON b.patient_id = p.id ORDER BY b.id DESC")->fetchAll();
$success = $_SESSION['success'] ?? ''; $error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>
<div class="topbar">
    <div class="topbar-left"><h1><?= $viewId ? 'Bill Details' : 'Billing' ?></h1><div class="breadcrumb">Dashboard / Billing</div></div>
    <div class="topbar-right">
        <?php if($viewId): ?>
        <a href="print_bill.php?id=<?= $viewId ?>" class="btn btn-outline btn-sm" target="_blank"><i class="fa fa-print"></i> Print</a>
        <a href="view_bill.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> All Bills</a>
        <?php else: ?>
        <a href="create_bill.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create Bill</a>
        <?php endif; ?>
    </div>
</div>
<div class="content-area">
<?php if($success): ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<?php if($viewId && !empty($bill)): ?>
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">
  <div class="card">
    <div class="card-header">
      <h3><i class="fa fa-file-invoice-dollar" style="color:var(--success);margin-right:7px"></i>Bill #<?= $bill['id'] ?></h3>
      <span class="badge <?= $bill['status']==='paid'?'badge-success':'badge-danger' ?>" style="font-size:13px;padding:5px 14px;"><?= ucfirst($bill['status']) ?></span>
    </div>
    <div class="card-body">
      <div style="background:var(--surface2);border-radius:10px;padding:16px;margin-bottom:20px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:10px;">Patient Info</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
          <div><div style="font-size:11px;color:var(--text-muted)">Name</div><div style="font-weight:600"><?= htmlspecialchars($bill['patient_name']??'N/A') ?></div></div>
          <div><div style="font-size:11px;color:var(--text-muted)">Phone</div><div style="font-weight:600"><?= htmlspecialchars($bill['phone']??'—') ?></div></div>
          <div><div style="font-size:11px;color:var(--text-muted)">Age/Gender</div><div style="font-weight:600"><?= $bill['age']??'—' ?>y / <?= htmlspecialchars($bill['gender']??'—') ?></div></div>
        </div>
      </div>
      <?php $fees=[['Doctor Consultation','doctor_fee','fa-user-doctor','var(--primary)'],['Medicine / Pharmacy','medicine_fee','fa-pills','var(--secondary)'],['Room / Ward','room_fee','fa-bed','var(--accent)'],['Other Charges','other_fee','fa-receipt','var(--text-muted)']];
      foreach($fees as [$lbl,$key,$icon,$clr]): ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 0;border-bottom:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:32px;height:32px;background:var(--surface2);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fa <?= $icon ?>" style="color:<?= $clr ?>"></i></div>
          <span style="font-weight:500"><?= $lbl ?></span>
        </div>
        <span style="font-weight:600">₹<?= number_format($bill[$key]) ?></span>
      </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:18px 0 0;">
        <span style="font-size:16px;font-weight:700">Total</span>
        <span style="font-size:22px;font-weight:800;color:var(--primary)">₹<?= number_format($bill['total_amount']) ?></span>
      </div>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><h3>Actions</h3></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:8px;padding:16px;">
        <?php if($bill['status']==='unpaid'): ?>
        <a href="update_status.php?id=<?= $bill['id'] ?>&status=paid" class="btn btn-success" style="justify-content:center;" onclick="return confirm('Mark as paid?')"><i class="fa fa-check"></i> Mark as Paid</a>
        <?php else: ?>
        <a href="update_status.php?id=<?= $bill['id'] ?>&status=unpaid" class="btn btn-outline" style="justify-content:center;"><i class="fa fa-rotate-left"></i> Mark Unpaid</a>
        <?php endif; ?>
        <a href="print_bill.php?id=<?= $bill['id'] ?>" class="btn btn-primary" style="justify-content:center;" target="_blank"><i class="fa fa-print"></i> Print Bill</a>
        <a href="download_bill.php?id=<?= $bill['id'] ?>" class="btn btn-outline" style="justify-content:center;"><i class="fa fa-download"></i> Download PDF</a>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
<div class="card">
  <div class="card-header"><h3><i class="fa fa-file-invoice-dollar" style="color:var(--success);margin-right:7px"></i>All Bills <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($bills) ?>)</span></h3></div>
  <div class="table-wrap"><table><thead><tr><th>Bill#</th><th>Patient</th><th>Doctor Fee</th><th>Medicine</th><th>Room</th><th>Other</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
  <?php foreach($bills as $b): ?>
  <tr>
    <td style="color:var(--text-muted);font-weight:600">#<?= $b['id'] ?></td>
    <td style="font-weight:600"><?= htmlspecialchars($b['patient_name']??'N/A') ?></td>
    <td>₹<?= number_format($b['doctor_fee']) ?></td>
    <td>₹<?= number_format($b['medicine_fee']) ?></td>
    <td>₹<?= number_format($b['room_fee']) ?></td>
    <td>₹<?= number_format($b['other_fee']) ?></td>
    <td style="font-weight:700;color:var(--primary)">₹<?= number_format($b['total_amount']) ?></td>
    <td><span class="badge <?= $b['status']==='paid'?'badge-success':'badge-danger' ?>"><?= ucfirst($b['status']) ?></span></td>
    <td><div style="display:flex;gap:5px;">
      <a href="view_bill.php?id=<?= $b['id'] ?>" class="btn btn-outline btn-icon btn-sm"><i class="fa fa-eye"></i></a>
      <a href="print_bill.php?id=<?= $b['id'] ?>" class="btn btn-primary btn-icon btn-sm" target="_blank"><i class="fa fa-print"></i></a>
      <?php if($b['status']==='unpaid'): ?><a href="update_status.php?id=<?= $b['id'] ?>&status=paid" class="btn btn-success btn-icon btn-sm" onclick="return confirm('Mark paid?')"><i class="fa fa-check"></i></a><?php endif; ?>
    </div></td>
  </tr>
  <?php endforeach; ?>
  <?php if(empty($bills)): ?><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)"><i class="fa fa-file-invoice-dollar" style="font-size:30px;display:block;margin-bottom:10px;opacity:.2"></i>No bills found</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
<?php endif; ?>
</div>
<?php include '../../admin/partials/footer.php'; ?>
