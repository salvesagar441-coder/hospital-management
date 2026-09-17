<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';

$pageTitle = "Patients — LifeCare HMS";
$search = trim($_GET['search'] ?? '');

$query = "SELECT * FROM patients WHERE is_deleted = 0";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR phone LIKE ? OR address LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$patients = $stmt->fetchAll();

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php include '../../admin/partials/header.php'; ?>
<?php include '../../admin/partials/sidebar.php'; ?>

<div class="topbar">
    <div class="topbar-left">
        <h1>Patients</h1>
        <div class="breadcrumb">Dashboard / Patients</div>
    </div>
    <div class="topbar-right">
        <a href="/hospital-management-system/admin/patients/add_patient.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add Patient
        </a>
    </div>
</div>

<div class="content-area">
    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><i class="fa fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-bed-pulse" style="color:var(--primary);margin-right:7px"></i>All Patients <span style="font-weight:400;color:var(--text-muted);font-size:13px">(<?= count($patients) ?>)</span></h3>
            <form method="GET" style="display:flex;gap:10px;align-items:center;">
                <div class="search-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Search patients..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
                </div>
                <?php if ($search): ?>
                <a href="view_patients.php" class="btn btn-outline btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Age/Gender</th>
                        <th>Blood Group</th>
                        <th>Phone</th>
                        <th>Admission Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $i => $p): ?>
                    <tr>
                        <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="patient-avatar"><?= strtoupper(substr($p['name'], 0, 1)) ?></div>
                                <div>
                                    <div style="font-weight:600"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size:12px;color:var(--text-muted)"><?= htmlspecialchars($p['address'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= $p['age'] ?>y / <?= htmlspecialchars($p['gender'] ?? '') ?></td>
                        <td>
                            <?php if ($p['blood_group'] && $p['blood_group'] !== 'None'): ?>
                            <span class="badge badge-danger"><?= htmlspecialchars($p['blood_group']) ?></span>
                            <?php else: ?>
                            <span style="color:var(--text-muted)">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['phone'] ?? '') ?></td>
                        <td style="color:var(--text-muted)"><?= $p['date'] ? date('d M Y', strtotime($p['date'])) : '—' ?></td>
                        <td>
                            <span class="badge <?= $p['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <i class="fa fa-circle" style="font-size:7px"></i>
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="view_single.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-icon btn-sm" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="edit_patient.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-icon btn-sm" title="Edit">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="delete_patient.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-icon btn-sm" title="Delete"
                                   onclick="return confirm('Move this patient to bin?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($patients)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="fa fa-bed-pulse" style="font-size:32px;margin-bottom:10px;display:block;opacity:0.3"></i>
                            No patients found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../admin/partials/footer.php'; ?>
