<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

/* ===== DATABASE ===== */
$conn = new mysqli("127.0.0.1","root","","transport_db",3306);
if($conn->connect_error){
    die("DB Error: ".$conn->connect_error);
}

/* ===== AUTH ===== */
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

/* ===== EDIT MODES ===== */
$editAssignment = null;
$editDriver = null;

/* ===== DRIVER CRUD ===== */
if(isset($_POST['add_driver'])){
    $conn->query("INSERT INTO drivers(name,license_no,phone)
                  VALUES('$_POST[name]','$_POST[lic]','$_POST[phone]')");
    header("Location:tms.php"); exit;
}

if(isset($_GET['edit_driver'])){
    $editDriver = $conn->query("SELECT * FROM drivers WHERE id=".(int)$_GET['edit_driver'])->fetch_assoc();
}

if(isset($_POST['update_driver'])){
    $conn->query("UPDATE drivers SET
                  name='$_POST[name]',
                  license_no='$_POST[lic]',
                  phone='$_POST[phone]'
                  WHERE id=".(int)$_POST['id']);
    header("Location:tms.php"); exit;
}

if(isset($_GET['delete_driver'])){
    $conn->query("DELETE FROM drivers WHERE id=".(int)$_GET['delete_driver']);
    header("Location:tms.php"); exit;
}

/* ===== ASSIGNMENT CRUD ===== */
if(isset($_POST['assign_transport'])){
    $conn->query("INSERT INTO assignments(vehicle_id,driver_id,route_id)
                  VALUES('$_POST[vehicle]','$_POST[driver]','$_POST[route]')");
    header("Location:tms.php"); exit;
}

if(isset($_GET['edit_assignment'])){
    $editAssignment = $conn->query("SELECT * FROM assignments WHERE id=".(int)$_GET['edit_assignment'])->fetch_assoc();
}

if(isset($_POST['update_assignment'])){
    $conn->query("UPDATE assignments SET
                  vehicle_id='$_POST[vehicle]',
                  driver_id='$_POST[driver]',
                  route_id='$_POST[route]'
                  WHERE id=".(int)$_POST['id']);
    header("Location:tms.php"); exit;
}

if(isset($_GET['delete_assignment'])){
    $conn->query("DELETE FROM assignments WHERE id=".(int)$_GET['delete_assignment']);
    header("Location:tms.php"); exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div>Admin Dashboard</div>
    <div>
        <a href="index.php">Home</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="routes.php">Routes</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

<section>
<div class="grid">

<div class="card">
<h3><?= $editAssignment ? "Edit Assignment" : "Assign Transport" ?></h3>
<form method="post">
<?php if($editAssignment){ ?>
<input type="hidden" name="id" value="<?= $editAssignment['id'] ?>">
<?php } ?>

<label>Vehicle</label>
<select name="vehicle" required>
<?php
$v=$conn->query("SELECT * FROM vehicles");
while($x=$v->fetch_assoc()){
$sel=($editAssignment && $editAssignment['vehicle_id']==$x['id'])?"selected":"";
echo "<option value='{$x['id']}' $sel>{$x['vehicle_no']}</option>";
}
?>
</select>

<label>Driver</label>
<select name="driver" required>
<?php
$d=$conn->query("SELECT * FROM drivers");
while($x=$d->fetch_assoc()){
$sel=($editAssignment && $editAssignment['driver_id']==$x['id'])?"selected":"";
echo "<option value='{$x['id']}' $sel>{$x['name']}</option>";
}
?>
</select>

<label>Route</label>
<select name="route" required>
<?php
$r=$conn->query("SELECT * FROM routes");
while($x=$r->fetch_assoc()){
$sel=($editAssignment && $editAssignment['route_id']==$x['id'])?"selected":"";
echo "<option value='{$x['id']}' $sel>{$x['source']} → {$x['destination']}</option>";
}
?>
</select>

<button name="<?= $editAssignment?'update_assignment':'assign_transport' ?>">
<?= $editAssignment?'Update Assignment':'Assign Transport' ?>
</button>
</form>
</div>

<div class="card">
<h3><?= $editDriver ? "Edit Driver" : "Add Driver" ?></h3>
<form method="post">
<?php if($editDriver){ ?>
<input type="hidden" name="id" value="<?= $editDriver['id'] ?>">
<?php } ?>

<label>Name</label>
<input name="name" value="<?= $editDriver['name'] ?? '' ?>" required>

<label>License</label>
<input name="lic" value="<?= $editDriver['license_no'] ?? '' ?>" required>

<label>Phone</label>
<input name="phone" value="<?= $editDriver['phone'] ?? '' ?>" required>

<button name="<?= $editDriver?'update_driver':'add_driver' ?>">
<?= $editDriver?'Update Driver':'Add Driver' ?>
</button>
</form>
</div>

</div>
</section>

<h2>Transport Assignments</h2>
<table>
<tr><th>Vehicle</th><th>Driver</th><th>Route</th><th>Actions</th></tr>
<?php
$q=$conn->query("
SELECT a.id,v.vehicle_no,d.name,
CONCAT(r.source,' → ',r.destination) route
FROM assignments a
JOIN vehicles v ON v.id=a.vehicle_id
JOIN drivers d ON d.id=a.driver_id
JOIN routes r ON r.id=a.route_id
");
while($row=$q->fetch_assoc()){
?>
<tr>
<td><?= $row['vehicle_no'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['route'] ?></td>
<td class="actions">

<form method="get">
<input type="hidden" name="edit_assignment" value="<?= $row['id'] ?>">
<button class="action-btn btn-edit">Edit</button>
</form>

<form method="get" onsubmit="return confirm('Delete assignment?')">
<input type="hidden" name="delete_assignment" value="<?= $row['id'] ?>">
<button class="action-btn btn-delete">Delete</button>
</form>

<a href="payment.php?id=<?= $row['id'] ?>" class="action-btn btn-pay">Pay</a>
<a href="invoice.php?id=<?= $row['id'] ?>" target="_blank" class="action-btn btn-invoice">Invoice</a>

</td>
</tr>
<?php } ?>
</table>

<h2>Driver Management</h2>
<table>
<tr><th>Name</th><th>License</th><th>Phone</th><th>Action</th></tr>
<?php
$d=$conn->query("SELECT * FROM drivers");
while($x=$d->fetch_assoc()){
?>
<tr>
<td><?= $x['name'] ?></td>
<td><?= $x['license_no'] ?></td>
<td><?= $x['phone'] ?></td>
<td class="actions">
<form method="get">
<input type="hidden" name="edit_driver" value="<?= $x['id'] ?>">
<button class="action-btn btn-edit">Edit</button>
</form>
<form method="get" onsubmit="return confirm('Delete driver?')">
<input type="hidden" name="delete_driver" value="<?= $x['id'] ?>">
<button class="action-btn btn-delete">Delete</button>
</form>
</td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
