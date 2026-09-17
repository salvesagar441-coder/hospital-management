<?php
require_once('middleware/auth_check.php');
require_once('../config/db.php');
$pageTitle = "Prescriptions — LifeCare HMS";

$rxs = $pdo->query("
    SELECT pr.*, 
           p.name as patient_name, p.age, p.gender, p.phone,
           u.name as doctor_name,
           a.date as appt_date,
           (SELECT COUNT(*) FROM prescription_items pi WHERE pi.prescription_id = pr.id) as med_count
    FROM prescriptions pr
    LEFT JOIN patients p ON pr.patient_id = p.id
    LEFT JOIN users u ON pr.doctor_id = u.id
    LEFT JOIN appointments a ON pr.appointment_id = a.id
    ORDER BY pr.created_at DESC
")->fetchAll();
?>
<?php include('partials/header.php'); ?>
<?php include('partials/sidebar.php'); ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Prescriptions</h1>
        <div class="breadcrumb">Dashboard / Prescriptions</div>
    </div>
</div>

<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-file-medical" style="color:var(--success);margin-right:7px"></i>All Prescriptions <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($rxs) ?>)</span></h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Appointment Date</th>
                        <th>Medicines</th>
                        <th>Issued On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rxs as $i => $rx): ?>
                    <tr>
                        <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="patient-avatar"><?= strtoupper(substr($rx['patient_name'] ?? 'P', 0, 1)) ?></div>
                                <div>
                                    <div style="font-weight:600"><?= htmlspecialchars($rx['patient_name'] ?? 'N/A') ?></div>
                                    <div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($rx['phone'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight:500">Dr. <?= htmlspecialchars($rx['doctor_name'] ?? 'N/A') ?></td>
                        <td style="color:var(--text-muted)"><?= $rx['appt_date'] ? date('d M Y', strtotime($rx['appt_date'])) : '—' ?></td>
                        <td>
                            <span class="badge badge-success">
                                <i class="fa fa-pills"></i> <?= $rx['med_count'] ?> medicine<?= $rx['med_count'] != 1 ? 's' : '' ?>
                            </span>
                        </td>
                        <td style="color:var(--text-muted)"><?= $rx['created_at'] ? date('d M Y', strtotime($rx['created_at'])) : '—' ?></td>
                        <td>
                            <a href="/hospital-management-system/doctor/print_prescription.php?id=<?= $rx['id'] ?>" class="btn btn-outline btn-icon btn-sm" title="View & Print" target="_blank">
                                <i class="fa fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rxs)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fa fa-file-medical" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3"></i>
                            No prescriptions found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>
