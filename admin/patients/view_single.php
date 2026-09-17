<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Patient Details — LifeCare HMS";

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: view_patients.php"); exit(); }

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ? AND is_deleted = 0");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Patient not found.'; header("Location: view_patients.php"); exit(); }

$bills = $pdo->prepare("SELECT * FROM billing WHERE patient_id = ? ORDER BY id DESC");
$bills->execute([$id]); $bills = $bills->fetchAll();

$appts = $pdo->prepare("SELECT a.*, u.name as doctor_name FROM appointments a LEFT JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ? ORDER BY a.id DESC");
$appts->execute([$id]); $appts = $appts->fetchAll();
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>
<div class="topbar">
    <div class="topbar-left"><h1>Patient Profile</h1><div class="breadcrumb">Dashboard / Patients / <?= htmlspecialchars($p['name']) ?></div></div>
    <div class="topbar-right">
        <a href="edit_patient.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Edit</a>
        <a href="view_patients.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>
<div class="content-area">
  <div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">
    <div style="display:flex;flex-direction:column;gap:18px;">
      <div class="card">
        <div class="card-body" style="text-align:center;padding:30px 24px 20px;">
          <div style="width:68px;height:68px;background:linear-gradient(135deg,var(--primary-light),#BFDBFE);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:var(--primary-dark);margin:0 auto 12px;"><?= strtoupper(substr($p['name'],0,1)) ?></div>
          <h2 style="font-size:17px;font-weight:700;margin-bottom:4px;"><?= htmlspecialchars($p['name']) ?></h2>
          <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;"><?= htmlspecialchars($p['phone']) ?></p>
          <span class="badge <?= $p['status']==='active'?'badge-success':'badge-warning' ?>"><i class="fa fa-circle" style="font-size:7px"></i> <?= ucfirst($p['status']) ?></span>
        </div>
        <div style="border-top:1px solid var(--border);">
          <?php $rows=[['fa-venus-mars','Gender',$p['gender']??'—'],['fa-cake-candles','Age',$p['age'].' yrs'],['fa-droplet','Blood',($p['blood_group']&&$p['blood_group']!=='None')?$p['blood_group']:'Unknown'],['fa-ruler-vertical','Height',$p['height']?$p['height'].' cm':'—'],['fa-weight-scale','Weight',$p['weight']?$p['weight'].' kg':'—'],['fa-calendar','Admitted',$p['date']?date('d M Y',strtotime($p['date'])):'—']];
          foreach($rows as [$icon,$lbl,$val]): ?>
          <div style="display:flex;justify-content:space-between;padding:10px 18px;border-bottom:1px solid var(--border);">
            <span style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;"><i class="fa <?= $icon ?>" style="color:var(--primary);width:13px;text-align:center"></i><?= $lbl ?></span>
            <span style="font-size:13px;font-weight:600"><?= htmlspecialchars($val) ?></span>
          </div>
          <?php endforeach; ?>
          <div style="padding:12px 18px;"><div style="font-size:11px;color:var(--text-muted);margin-bottom:4px;"><i class="fa fa-location-dot" style="color:var(--primary);margin-right:5px"></i>Address</div><div style="font-size:13px;font-weight:500"><?= htmlspecialchars($p['address']??'—') ?></div></div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3>Quick Actions</h3></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:8px;padding:16px;">
          <a href="/hospital-management-system/admin/billing/create_bill.php?patient_id=<?= $p['id'] ?>" class="btn btn-success" style="justify-content:center;"><i class="fa fa-file-invoice-dollar"></i> Create Bill</a>
          <a href="/hospital-management-system/admin/appointments/create_appointment.php?patient_id=<?= $p['id'] ?>" class="btn btn-primary" style="justify-content:center;"><i class="fa fa-calendar-plus"></i> Book Appointment</a>
          <a href="edit_patient.php?id=<?= $p['id'] ?>" class="btn btn-warning" style="justify-content:center;"><i class="fa fa-pen"></i> Edit Patient</a>
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:18px;">
      <div class="card">
        <div class="card-header"><h3><i class="fa fa-file-invoice-dollar" style="color:var(--success);margin-right:6px"></i>Billing History</h3><a href="/hospital-management-system/admin/billing/create_bill.php?patient_id=<?= $p['id'] ?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> New Bill</a></div>
        <div class="table-wrap"><table><thead><tr><th>Bill#</th><th>Doctor Fee</th><th>Medicine</th><th>Room</th><th>Other</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>
          <?php foreach($bills as $b): ?>
          <tr>
            <td style="color:var(--text-muted)">#<?= $b['id'] ?></td>
            <td>₹<?= number_format($b['doctor_fee']) ?></td>
            <td>₹<?= number_format($b['medicine_fee']) ?></td>
            <td>₹<?= number_format($b['room_fee']) ?></td>
            <td>₹<?= number_format($b['other_fee']) ?></td>
            <td style="font-weight:700;color:var(--primary)">₹<?= number_format($b['total_amount']) ?></td>
            <td><span class="badge <?= $b['status']==='paid'?'badge-success':'badge-danger' ?>"><?= ucfirst($b['status']) ?></span></td>
            <td><a href="/hospital-management-system/admin/billing/view_bill.php?id=<?= $b['id'] ?>" class="btn btn-outline btn-icon btn-sm"><i class="fa fa-eye"></i></a></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($bills)): ?><tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text-muted)">No bills yet</td></tr><?php endif; ?>
        </tbody></table></div>
      </div>
      <div class="card">
        <div class="card-header"><h3><i class="fa fa-calendar-check" style="color:var(--accent);margin-right:6px"></i>Appointments</h3><a href="/hospital-management-system/admin/appointments/create_appointment.php?patient_id=<?= $p['id'] ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Book</a></div>
        <div class="table-wrap"><table><thead><tr><th>Date & Time</th><th>Doctor</th><th>Status</th></tr></thead><tbody>
          <?php foreach($appts as $a): ?>
          <tr>
            <td><?= $a['date']?date('d M Y, h:i A',strtotime($a['date'])):'—' ?></td>
            <td><?= htmlspecialchars($a['doctor_name']??'N/A') ?></td>
            <td><span class="badge <?= $a['status']==='confirmed'?'badge-success':($a['status']==='pending'?'badge-warning':'badge-gray') ?>"><?= ucfirst($a['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($appts)): ?><tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-muted)">No appointments</td></tr><?php endif; ?>
        </tbody></table></div>
      </div>
    </div>
  </div>
</div>
<?php include '../../admin/partials/footer.php'; ?>
