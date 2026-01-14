<?php
session_start();

/* ---------------- ERROR REPORTING (DEV) ---------------- */
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ---------------- DB CONNECTION ---------------- */
$conn = new mysqli("localhost", "root", "", "transport_db");
if ($conn->connect_error) {
    die("Database Connection Failed");
}

/* ---------------- AUTH CHECK ---------------- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* ---------------- VARIABLES ---------------- */
$msg = "";
$editVehicle = null;
$image = "";

/* ---------------- IMAGE UPLOAD ---------------- */
if (!empty($_FILES['image']['name'])) {
    $image = time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
}

/* ---------------- ADD VEHICLE ---------------- */
if (isset($_POST['add_vehicle'])) {

    $conn->query("
        INSERT INTO vehicles
        (vehicle_no, type, capacity, description, fuel_type, image)
        VALUES (
            '{$_POST['vno']}',
            '{$_POST['type']}',
            '{$_POST['cap']}',
            '{$_POST['desc']}',
            '{$_POST['fuel']}',
            '$image'
        )
    ");

    $msg = "Vehicle Added Successfully";
}

/* ---------------- EDIT VEHICLE ---------------- */
if (isset($_GET['edit_vehicle'])) {
    $id = (int)$_GET['edit_vehicle'];
    $editVehicle = $conn->query("SELECT * FROM vehicles WHERE id=$id")->fetch_assoc();
}

/* ---------------- UPDATE VEHICLE ---------------- */
if (isset($_POST['update_vehicle'])) {
    $id = (int)$_POST['id'];

    if (empty($image)) {
        $old = $conn->query("SELECT image FROM vehicles WHERE id=$id")->fetch_assoc();
        $image = $old['image'];
    }

    $conn->query("
        UPDATE vehicles SET
            vehicle_no='{$_POST['vno']}',
            type='{$_POST['type']}',
            capacity='{$_POST['cap']}',
            description='{$_POST['desc']}',
            fuel_type='{$_POST['fuel']}',
            image='$image'
        WHERE id=$id
    ");

    header("Location: vehicles.php");
    exit;
}

/* ---------------- DELETE VEHICLE ---------------- */
if (isset($_GET['delete_vehicle'])) {
    $id = (int)$_GET['delete_vehicle'];
    $conn->query("DELETE FROM vehicles WHERE id=$id");
    header("Location: vehicles.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vehicle Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div>Vehicle Management</div>
    <div>
        <a href="index.php">Home</a>
        <a href="tms.php">Dashboard</a>
        <a href="routes.php">Routes</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

<section>

<div class="grid">

<!-- ================= ADD / EDIT VEHICLE ================= -->
<div class="card">

<h3><?= $editVehicle ? "Edit Vehicle" : "Add Vehicle" ?></h3>

<form method="POST" enctype="multipart/form-data">

<?php if ($editVehicle): ?>
<input type="hidden" name="id" value="<?= $editVehicle['id'] ?>">
<?php endif; ?>

<label>Vehicle Number</label>
<input name="vno" required value="<?= $editVehicle['vehicle_no'] ?? '' ?>">

<label>Vehicle Type</label>
<input name="type" required value="<?= $editVehicle['type'] ?? '' ?>">

<label>Capacity</label>
<input name="cap" required value="<?= $editVehicle['capacity'] ?? '' ?>">

<label>Description</label>
<input name="desc" value="<?= $editVehicle['description'] ?? '' ?>">

<label>Fuel Type</label>
<select name="fuel" required>
    <option value="">Select Fuel</option>
    <option value="Petrol" <?= ($editVehicle && $editVehicle['fuel_type']=="Petrol") ? "selected" : "" ?>>Petrol</option>
    <option value="Diesel" <?= ($editVehicle && $editVehicle['fuel_type']=="Diesel") ? "selected" : "" ?>>Diesel</option>
    <option value="Electric" <?= ($editVehicle && $editVehicle['fuel_type']=="Electric") ? "selected" : "" ?>>Electric</option>
</select>

<label>Vehicle Image</label>
<input type="file" name="image">

<button name="<?= $editVehicle ? 'update_vehicle' : 'add_vehicle' ?>">
    <?= $editVehicle ? 'Update Vehicle' : 'Add Vehicle' ?>
</button>

<?php if ($editVehicle): ?>
<a href="vehicles.php" class="btn" style="background:#888;">Cancel</a>
<?php endif; ?>

<?php if ($msg): ?>
<p style="color:green;margin-top:10px"><?= $msg ?></p>
<?php endif; ?>

</form>
</div>

</div>

</section>

<!-- ================= VEHICLE LIST ================= -->
<h2 style="text-align:center;">Vehicle List</h2>

<table>
<tr>
    <th>Image</th>
    <th>Vehicle No</th>
    <th>Type</th>
    <th>Fuel</th>
    <th>Capacity</th>
    <th>Action</th>
</tr>

<?php
$q = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");

if ($q->num_rows == 0) {
    echo "<tr><td colspan='6' align='center'>No Vehicles Found</td></tr>";
}

while ($v = $q->fetch_assoc()):
    $img = $v['image'] ? "uploads/{$v['image']}" : "https://via.placeholder.com/80";
?>
<tr>
    <td>
        <img src="<?= $img ?>" width="80" style="border-radius:8px;">
    </td>
    <td><?= $v['vehicle_no'] ?></td>
    <td><?= $v['type'] ?></td>
    <td><?= $v['fuel_type'] ?></td>
    <td><?= $v['capacity'] ?></td>
    <td>
        <a href="?edit_vehicle=<?= $v['id'] ?>" class="action-btn btn-edit">
            Edit
        </a>

        <a href="?delete_vehicle=<?= $v['id'] ?>"
           class="action-btn btn-delete"
           onclick="return confirm('Delete this vehicle?')">
            Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>
