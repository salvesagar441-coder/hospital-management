<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /hospital-management-system/auth/login.php");
    exit();
}
require_once('../config/db.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: /hospital-management-system/doctor/prescriptions.php"); exit(); }

$stmt = $pdo->prepare("
    SELECT pr.*, 
           p.name as patient_name, p.age, p.gender, p.phone, p.address, p.blood_group,
           u.name as doctor_name,
           a.date as appt_date
    FROM prescriptions pr
    LEFT JOIN patients p ON pr.patient_id = p.id
    LEFT JOIN users u ON pr.doctor_id = u.id
    LEFT JOIN appointments a ON pr.appointment_id = a.id
    WHERE pr.id = ?
");
$stmt->execute([$id]);
$rx = $stmt->fetch();
if (!$rx) { echo "Prescription not found."; exit(); }

// Get medicines
$meds = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id = ?");
$meds->execute([$id]);
$medicines = $meds->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription #<?= $rx['id'] ?> — LifeCare HMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:#f5f5f5; color:#1a202c; font-size:14px; }

        .print-btn-bar { text-align:center; padding:20px; background:#fff; border-bottom:1px solid #e2e8f0; }
        .print-btn-bar button { background:#0A6EBD; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; font-family:inherit; margin-right:10px; }
        .print-btn-bar a { color:#64748b; font-size:13px; text-decoration:none; }

        .rx { max-width:780px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.1); }

        /* Header */
        .rx-header { background:linear-gradient(135deg,#0A6EBD,#00B4A6); padding:28px 36px; display:flex; justify-content:space-between; align-items:flex-start; }
        .rx-header .hosp-name { font-size:22px; font-weight:800; color:#fff; }
        .rx-header .hosp-sub { font-size:12px; color:rgba(255,255,255,0.75); margin-top:4px; }
        .rx-header .rx-no { text-align:right; }
        .rx-header .rx-no .num { font-size:18px; font-weight:800; color:#fff; }
        .rx-header .rx-no .date { font-size:12px; color:rgba(255,255,255,0.7); margin-top:4px; }

        .rx-body { padding:28px 36px; }

        /* Patient + Doctor info */
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
        .info-box { background:#f8fafc; border-radius:10px; padding:16px; border:1px solid #e2e8f0; }
        .info-box .box-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#94a3b8; margin-bottom:10px; }
        .info-row { display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
        .info-row:last-child { border-bottom:none; }
        .info-row .lbl { color:#64748b; }
        .info-row .val { font-weight:600; color:#1a202c; }

        /* Rx Symbol */
        .rx-symbol { display:flex; align-items:center; gap:12px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #e2e8f0; }
        .rx-symbol .symbol { font-size:32px; font-weight:900; color:#0A6EBD; font-style:italic; line-height:1; }
        .rx-symbol .rx-label { font-size:14px; font-weight:600; color:#64748b; }

        /* Notes */
        .notes-box { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:14px 16px; margin-bottom:20px; }
        .notes-box .notes-title { font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px; }
        .notes-box .notes-text { font-size:13.5px; color:#1a202c; line-height:1.6; }

        /* Medicines table */
        .med-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
        .med-table thead tr { background:#0A6EBD; }
        .med-table thead th { padding:10px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#fff; text-align:left; }
        .med-table tbody tr { border-bottom:1px solid #f1f5f9; }
        .med-table tbody tr:nth-child(even) { background:#f8fafc; }
        .med-table tbody td { padding:12px 14px; font-size:13px; color:#1a202c; vertical-align:top; }
        .med-table tbody td .med-name { font-weight:700; font-size:14px; }
        .med-table tbody td .med-instructions { font-size:11.5px; color:#64748b; margin-top:2px; font-style:italic; }
        .sno { width:36px; color:#94a3b8; font-weight:600; }
        .badge-freq { display:inline-block; background:#EFF6FF; color:#1d4ed8; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; }

        /* Footer */
        .rx-footer { display:flex; justify-content:space-between; align-items:flex-end; padding-top:20px; border-top:1px solid #e2e8f0; margin-top:10px; }
        .rx-footer .sign-area { text-align:right; }
        .rx-footer .sign-line { width:180px; border-top:2px solid #1a202c; margin-bottom:6px; }
        .rx-footer .sign-label { font-size:12px; color:#64748b; }
        .rx-footer .sign-name { font-size:14px; font-weight:700; }
        .rx-footer .issued { font-size:12px; color:#94a3b8; }

        .doc-note { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; font-size:12px; color:#166534; margin-top:16px; text-align:center; }

        @media print {
            .print-btn-bar { display:none !important; }
            body { background:#fff; }
            .rx { box-shadow:none; margin:0; border-radius:0; }
        }
    </style>
</head>
<body>

<div class="print-btn-bar no-print">
    <button onclick="window.print()">🖨️ Print Prescription</button>
    <a href="javascript:history.back()">← Back</a>
</div>

<div class="rx">
    <!-- Header -->
    <div class="rx-header">
        <div>
            <div class="hosp-name">🏥 LifeCare Hospital</div>
            <div class="hosp-sub">Chhatrapati Sambhajinagar, Maharashtra</div>
            <div class="hosp-sub">📞 +91 9876543210 | lifecarehospital.in</div>
        </div>
        <div class="rx-no">
            <div class="num">Prescription #<?= str_pad($rx['id'], 4, '0', STR_PAD_LEFT) ?></div>
            <div class="date">Date: <?= date('d M Y', strtotime($rx['created_at'] ?? 'now')) ?></div>
            <div class="date">Appt: <?= $rx['appt_date'] ? date('d M Y', strtotime($rx['appt_date'])) : '—' ?></div>
        </div>
    </div>

    <div class="rx-body">
        <!-- Patient + Doctor Info -->
        <div class="info-grid">
            <div class="info-box">
                <div class="box-title">Patient Information</div>
                <div class="info-row"><span class="lbl">Name</span><span class="val"><?= htmlspecialchars($rx['patient_name']) ?></span></div>
                <div class="info-row"><span class="lbl">Age / Gender</span><span class="val"><?= $rx['age'] ?>y / <?= htmlspecialchars($rx['gender'] ?? '—') ?></span></div>
                <div class="info-row"><span class="lbl">Phone</span><span class="val"><?= htmlspecialchars($rx['phone'] ?? '—') ?></span></div>
                <div class="info-row"><span class="lbl">Blood Group</span><span class="val"><?= ($rx['blood_group'] && $rx['blood_group'] !== 'None') ? htmlspecialchars($rx['blood_group']) : '—' ?></span></div>
            </div>
            <div class="info-box">
                <div class="box-title">Doctor Information</div>
                <div class="info-row"><span class="lbl">Doctor</span><span class="val">Dr. <?= htmlspecialchars($rx['doctor_name']) ?></span></div>
                <div class="info-row"><span class="lbl">Hospital</span><span class="val">LifeCare Hospital</span></div>
                <div class="info-row"><span class="lbl">Date</span><span class="val"><?= date('d M Y') ?></span></div>
                <div class="info-row"><span class="lbl">Rx No.</span><span class="val">#<?= str_pad($rx['id'], 4, '0', STR_PAD_LEFT) ?></span></div>
            </div>
        </div>

        <!-- Diagnosis Notes -->
        <?php if (!empty($rx['notes'])): ?>
        <div class="notes-box">
            <div class="notes-title">📋 Diagnosis / Notes</div>
            <div class="notes-text"><?= nl2br(htmlspecialchars($rx['notes'])) ?></div>
        </div>
        <?php endif; ?>

        <!-- Rx Symbol + Medicines -->
        <div class="rx-symbol">
            <div class="symbol">Rx</div>
            <div class="rx-label">Prescription Medicines</div>
        </div>

        <table class="med-table">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th>Medicine Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicines as $i => $med): ?>
                <tr>
                    <td class="sno"><?= $i + 1 ?></td>
                    <td>
                        <div class="med-name"><?= htmlspecialchars($med['medicine_name']) ?></div>
                        <?php if (!empty($med['instructions'])): ?>
                        <div class="med-instructions">📌 <?= htmlspecialchars($med['instructions']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($med['dosage'] ?: '—') ?></td>
                    <td>
                        <?php if ($med['frequency']): ?>
                        <span class="badge-freq"><?= htmlspecialchars($med['frequency']) ?></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($med['duration'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($medicines)): ?>
                <tr><td colspan="5" style="text-align:center;padding:20px;color:#94a3b8;">No medicines prescribed</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="rx-footer">
            <div class="issued">
                <div>Issued: <?= date('d M Y, h:i A') ?></div>
                <div style="margin-top:4px;">LifeCare Hospital Management System</div>
            </div>
            <div class="sign-area">
                <div class="sign-line"></div>
                <div class="sign-name">Dr. <?= htmlspecialchars($rx['doctor_name']) ?></div>
                <div class="sign-label">Doctor's Signature</div>
            </div>
        </div>

        <div class="doc-note">⚠️ This prescription is valid for 7 days from the date of issue. Take medicines as directed by the doctor.</div>
    </div>
</div>

</body>
</html>
