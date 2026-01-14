<?php
session_start();
$conn = new mysqli("127.0.0.1","root","","transport_db",3306);

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];

$q = $conn->query("
SELECT a.*, 
v.vehicle_no, v.type,
d.name AS driver, d.phone,
r.source, r.destination
FROM assignments a
JOIN vehicles v ON v.id=a.vehicle_id
JOIN drivers d ON d.id=a.driver_id
JOIN routes r ON r.id=a.route_id
WHERE a.id=$id
");

$row = $q->fetch_assoc();
$date = date("d M Y");
?>
<!DOCTYPE html>
<html>
<head>
<title>Invoice</title>

<style>
body{
    font-family: 'Segoe UI', Arial, sans-serif;
    background:#f4f6f9;
}

.invoice{
    width:800px;
    margin:30px auto;
    background:#fff;
    padding:30px;
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    border-bottom:2px solid #1d2671;
    padding-bottom:15px;
}

.company h2{
    margin:0;
    color:#1d2671;
}

.company p{
    margin:3px 0;
    font-size:14px;
    color:#555;
}

.invoice-info{
    text-align:right;
    font-size:14px;
}

/* STATUS */
.status{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    font-weight:600;
    font-size:13px;
}

.paid{
    background:#d4edda;
    color:#155724;
}

.pending{
    background:#f8d7da;
    color:#721c24;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}

th{
    background:#1d2671;
    color:#fff;
    padding:12px;
    text-align:left;
    font-size:14px;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

/* TOTAL */
.total{
    text-align:right;
    font-size:18px;
    font-weight:700;
    margin-top:20px;
}

/* FOOTER */
.footer{
    margin-top:30px;
    font-size:13px;
    color:#666;
    text-align:center;
}

/* PRINT */
.print{
    text-align:center;
    margin-top:25px;
}

.print button{
    padding:10px 22px;
    font-size:15px;
    border:none;
    background:#1d2671;
    color:#fff;
    border-radius:6px;
    cursor:pointer;
}

@media print{
    body{background:#fff;}
    .print{display:none;}
}
</style>

</head>

<body>

<div class="invoice">

<!-- HEADER -->
<div class="header">
    <div class="company">
        <h2>Transport Management System</h2>
        <p>Avinashi Road, Coimbatore</p>
        <p>Email: support@tms.com</p>
        <p>Phone: +91 98765 43210</p>
    </div>

    <div class="invoice-info">
        <p><b>Invoice #</b> TMS-<?= $row['id'] ?></p>
        <p><b>Date:</b> <?= $date ?></p>
        <span class="status <?= $row['payment_status']=="Paid"?"paid":"pending" ?>">
            <?= strtoupper($row['payment_status']) ?>
        </span>
    </div>
</div>

<!-- DETAILS -->
<table>
<tr>
    <th>Logistics Detail</th>
    <th>Information</th>
</tr>
<tr>
    <td>Vehicle</td>
    <td><?= $row['vehicle_no'] ?> (<?= $row['type'] ?>)</td>
</tr>
<tr>
    <td>Driver</td>
    <td><?= $row['driver'] ?> | <?= $row['phone'] ?></td>
</tr>
<tr>
    <td>Route</td>
    <td><?= $row['source'] ?> → <?= $row['destination'] ?></td>
</tr>
<tr>
    <td>Payment Status</td>
    <td><?= $row['payment_status'] ?></td>
</tr>
</table>

<!-- TOTAL -->
<div class="total">
    Total Amount: ₹ <?= number_format($row['payment_amount']) ?>
</div>

<!-- FOOTER -->
<div class="footer">
    Thank you for choosing our transport service.<br>
    This is a system generated invoice.
</div>

<!-- PRINT -->
<div class="print">
    <button onclick="window.print()">Print / Download Invoice</button>
</div>

</div>

</body>
</html>
