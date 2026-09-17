<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Appointments — LifeCare HMS";
$appts = $pdo->query("SELECT a.*, p.name as patient_name, u.name as doctor_name FROM appointments a LEFT JOIN patients p ON a.patient_id=p.id LEFT JOIN users u ON a.doctor_id=u.id ORDER BY a.id DESC")->fetchAll();
$success = $_SESSION['success'] ?? ''; $error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>
<div class="topbar">
    <div class="topbar-left"><h1>Appointments</h1><div class="breadcrumb">Dashboard / Appointments</div></div>
    <div class="topbar-right"><a href="create_appointment.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Book Appointment</a></div>
</div>
<div class="content-area">
<?php if($success): ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="card">
    <div class="card-header"><h3><i class="fa fa-calendar-check" style="color:var(--accent);margin-right:7px"></i>All Appointments <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($appts) ?>)</span></h3></div>
    <div class="table-wrap"><table><thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Date & Time</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($appts as $i=>$a): ?>
    <tr>
        <td style="color:var(--text-muted)"><?= $i+1 ?></td>
        <td style="font-weight:600"><?= htmlspecialchars($a['patient_name']??'N/A') ?></td>
        <td><?= htmlspecialchars($a['doctor_name']??'N/A') ?></td>
        <td style="color:var(--text-muted)"><?= $a['date']?date('d M Y, h:i A',strtotime($a['date'])):'—' ?></td>
        <td>
            <span class="badge <?= $a['status']==='confirmed'?'badge-success':($a['status']==='pending'?'badge-warning':($a['status']==='cancelled'?'badge-danger':'badge-gray')) ?>">
                <?= ucfirst($a['status']) ?>
            </span>
        </td>
        <td>
            <div style="display:flex;gap:5px;">
                <?php if($a['status']==='pending'): ?>
                <a href="update_appointment.php?id=<?= $a['id'] ?>&status=confirmed" class="btn btn-success btn-sm" onclick="return confirm('Confirm appointment?')"><i class="fa fa-check"></i> Confirm</a>
                <a href="update_appointment.php?id=<?= $a['id'] ?>&status=cancelled" class="btn btn-danger btn-sm" onclick="return confirm('Cancel appointment?')"><i class="fa fa-xmark"></i> Cancel</a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($appts)): ?><tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted)"><i class="fa fa-calendar-check" style="font-size:30px;display:block;margin-bottom:10px;opacity:.2"></i>No appointments yet</td></tr><?php endif; ?>
    </tbody></table></div>
</div>
</div>
<?php include '../../admin/partials/footer.php'; ?>
