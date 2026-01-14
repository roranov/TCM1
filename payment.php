<?php
session_start();
$conn = new mysqli("localhost","root","","transport_db");

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];

/* Get assignment details */
$q = $conn->query("
SELECT a.*, v.vehicle_no, d.name, 
CONCAT(r.source,' → ',r.destination) AS route
FROM assignments a
JOIN vehicles v ON v.id=a.vehicle_id
JOIN drivers d ON d.id=a.driver_id
JOIN routes r ON r.id=a.route_id
WHERE a.id=$id
");

$data = $q->fetch_assoc();

/* Pay */
if(isset($_POST['pay'])){
    $conn->query("
    UPDATE assignments SET
    payment_status='Paid',
    payment_amount='{$_POST['amount']}'
    WHERE id=$id
    ");
    header("Location: tms.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="navbar">
    <div>Payment</div>
    <div>
        <a href="tms.php">Back</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

<section>
<div class="card" style="max-width:500px;margin:auto;">

<h3>Transport Payment</h3>

<p><b>Vehicle:</b> <?= $data['vehicle_no'] ?></p>
<p><b>Driver:</b> <?= $data['name'] ?></p>
<p><b>Route:</b> <?= $data['route'] ?></p>

<form method="POST">
<label>Amount (₹)</label>
<input name="amount" value="1500" required>

<button name="pay">Pay Now</button>
</form>

</div>
</section>

</div>

</body>
</html>
