<?php
require_once '../../admin/middleware/auth_check.php';
require_once '../../config/db.php';
$id = (int)($_GET['id'] ?? 0);
if(!$id){header("Location: view_bill.php");exit();}
$stmt = $pdo->prepare("SELECT b.*, p.name as patient_name, p.phone, p.address, p.age, p.gender, p.blood_group FROM billing b LEFT JOIN patients p ON b.patient_id=p.id WHERE b.id=?");
$stmt->execute([$id]); $b = $stmt->fetch();
if(!$b){header("Location: view_bill.php");exit();}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bill #<?= $b['id'] ?> — LifeCare HMS</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;color:#1a202c;background:#fff;padding:32px;}
.bill-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;padding-bottom:20px;border-bottom:2px solid #0A6EBD;}
.hospital-name{font-size:22px;font-weight:800;color:#0A6EBD;}
.hospital-sub{font-size:12px;color:#64748b;margin-top:3px;}
.bill-meta{text-align:right;}
.bill-meta .bill-num{font-size:20px;font-weight:800;color:#0A6EBD;}
.bill-meta .bill-date{font-size:12px;color:#64748b;}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin-bottom:10px;}
.patient-box{background:#f8fafc;border-radius:8px;padding:14px 18px;margin-bottom:22px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.patient-box .field label{font-size:10px;color:#94a3b8;display:block;margin-bottom:2px;}
.patient-box .field span{font-weight:600;font-size:13px;}
.fee-row{display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px solid #e2e8f0;}
.fee-row:last-child{border:none;}
.fee-row .name{color:#475569;}
.fee-row .amount{font-weight:600;}
.total-row{display:flex;justify-content:space-between;padding:16px 18px;background:#EFF6FF;border-radius:8px;margin-top:10px;}
.total-row .label{font-size:15px;font-weight:700;color:#1e40af;}
.total-row .amount{font-size:20px;font-weight:800;color:#0A6EBD;}
.status-badge{display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-top:14px;}
.paid{background:#f0fdf4;color:#16a34a;}
.unpaid{background:#fef2f2;color:#dc2626;}
.footer{margin-top:36px;text-align:center;font-size:11px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:14px;}
@media print{body{padding:16px;} .no-print{display:none;}}
</style>
</head>
<body>
<div class="no-print" style="margin-bottom:20px;display:flex;gap:10px;">
    <button onclick="window.print()" style="background:#0A6EBD;color:#fff;border:none;padding:9px 20px;border-radius:7px;font-weight:600;cursor:pointer;font-family:inherit;font-size:13px;"><i>🖨</i> Print</button>
    <a href="view_bill.php?id=<?= $b['id'] ?>" style="background:#f1f5f9;color:#475569;border:none;padding:9px 20px;border-radius:7px;font-weight:600;cursor:pointer;text-decoration:none;font-size:13px;">← Back</a>
</div>

<div class="bill-header">
    <div>
        <div class="hospital-name">🏥 LifeCare Hospital</div>
        <div class="hospital-sub">Chhatrapati Sambhajinagar, Maharashtra</div>
        <div class="hospital-sub">Phone: +91 9876543210 | lifecarehospital.in</div>
    </div>
    <div class="bill-meta">
        <div class="bill-num">BILL #<?= str_pad($b['id'],4,'0',STR_PAD_LEFT) ?></div>
        <div class="bill-date">Date: <?= date('d M Y') ?></div>
        <div class="bill-date">Time: <?= date('h:i A') ?></div>
    </div>
</div>

<div class="section-title">Patient Information</div>
<div class="patient-box">
    <div class="field"><label>Patient Name</label><span><?= htmlspecialchars($b['patient_name']??'N/A') ?></span></div>
    <div class="field"><label>Phone</label><span><?= htmlspecialchars($b['phone']??'—') ?></span></div>
    <div class="field"><label>Age / Gender</label><span><?= $b['age']??'—' ?>y / <?= htmlspecialchars($b['gender']??'—') ?></span></div>
    <div class="field" style="grid-column:1/-1"><label>Address</label><span><?= htmlspecialchars($b['address']??'—') ?></span></div>
</div>

<div class="section-title">Charges Breakdown</div>
<div class="fee-row"><span class="name">Doctor Consultation Fee</span><span class="amount">₹<?= number_format($b['doctor_fee']) ?></span></div>
<div class="fee-row"><span class="name">Medicine / Pharmacy</span><span class="amount">₹<?= number_format($b['medicine_fee']) ?></span></div>
<div class="fee-row"><span class="name">Room / Ward Charges</span><span class="amount">₹<?= number_format($b['room_fee']) ?></span></div>
<div class="fee-row"><span class="name">Other Charges</span><span class="amount">₹<?= number_format($b['other_fee']) ?></span></div>

<div class="total-row"><span class="label">Total Amount</span><span class="amount">₹<?= number_format($b['total_amount']) ?></span></div>
<div><span class="status-badge <?= $b['status'] ?>"><?= strtoupper($b['status']) ?></span></div>

<div class="footer">
    Thank you for choosing LifeCare Hospital • Get well soon!<br>
    This is a computer-generated bill and does not require a signature.
</div>
</body>
</html>
