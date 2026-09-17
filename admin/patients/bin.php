<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$pageTitle = "Patient Bin — LifeCare HMS";

$deleted = $pdo->query("SELECT * FROM patients WHERE is_deleted = 1 ORDER BY id DESC")->fetchAll();

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Patient Bin</h1>
        <div class="breadcrumb">Dashboard / Patients / Bin</div>
    </div>
    <div class="topbar-right">
        <a href="view_patients.php" class="btn btn-outline btn-sm"><i class="fa fa-arrow-left"></i> Back to Patients</a>
    </div>
</div>

<div class="content-area">
    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-trash-can" style="color:var(--danger);margin-right:7px"></i>Deleted Patients <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($deleted) ?>)</span></h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient Name</th>
                        <th>Age/Gender</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deleted as $i => $p): ?>
                    <tr>
                        <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="patient-avatar" style="opacity:0.5"><?= strtoupper(substr($p['name'], 0, 1)) ?></div>
                                <span style="font-weight:600;color:var(--text-muted)"><?= htmlspecialchars($p['name']) ?></span>
                            </div>
                        </td>
                        <td><?= $p['age'] ?>y / <?= htmlspecialchars($p['gender'] ?? '') ?></td>
                        <td><?= htmlspecialchars($p['phone'] ?? '') ?></td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="restore_patient.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-rotate-left"></i> Restore
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($deleted)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fa fa-trash-can" style="font-size:32px;margin-bottom:10px;display:block;opacity:0.2"></i>
                            Bin is empty
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../admin/partials/footer.php'; ?>
