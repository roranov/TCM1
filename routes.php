<?php
session_start();

/* ---------------- DB CONNECTION ---------------- */
$conn = new mysqli("localhost","root","","transport_db");
if($conn->connect_error){
    die("Database Connection Failed");
}

/* ---------------- SESSION CHECK ---------------- */
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

/* ---------------- VARIABLES ---------------- */
$msg = "";
$editRoute = null;

/* ---------------- ADD ROUTE ---------------- */
if(isset($_POST['add_route'])){
    $conn->query("
        INSERT INTO routes (source, destination, distance)
        VALUES (
            '{$_POST['source']}',
            '{$_POST['destination']}',
            '{$_POST['distance']}'
        )
    ");
    $msg = "Route Added Successfully";
}

/* ---------------- EDIT ROUTE ---------------- */
if(isset($_GET['edit_route'])){
    $id = (int)$_GET['edit_route'];
    $editRoute = $conn->query("SELECT * FROM routes WHERE id=$id")->fetch_assoc();
}

/* ---------------- UPDATE ROUTE ---------------- */
if(isset($_POST['update_route'])){
    $id = (int)$_POST['id'];
    $conn->query("
        UPDATE routes SET
            source='{$_POST['source']}',
            destination='{$_POST['destination']}',
            distance='{$_POST['distance']}'
        WHERE id=$id
    ");
    header("Location: routes.php");
    exit;
}

/* ---------------- DELETE ROUTE ---------------- */
if(isset($_GET['delete_route'])){
    $id = (int)$_GET['delete_route'];
    $conn->query("DELETE FROM routes WHERE id=$id");
    header("Location: routes.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Route Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div>Route Management</div>
    <div>
        <a href="index.php">Home</a>
        <a href="tms.php">Dashboard</a>
        <a href="vehicles.php">Vehicles</a>
        <a href="routes.php">Routes</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

<section>

<div class="grid">

<!-- ================= ADD / EDIT ROUTE ================= -->
<div class="card">

<h3><?= $editRoute ? "Edit Route" : "Add Route" ?></h3>

<form method="POST">

<?php if($editRoute): ?>
<input type="hidden" name="id" value="<?= $editRoute['id'] ?>">
<?php endif; ?>

<label>Source</label>
<input name="source" required value="<?= $editRoute['source'] ?? '' ?>">

<label>Destination</label>
<input name="destination" required value="<?= $editRoute['destination'] ?? '' ?>">

<label>Distance</label>
<input name="distance" placeholder="Eg: 120 km" required
       value="<?= $editRoute['distance'] ?? '' ?>">

<button name="<?= $editRoute ? 'update_route' : 'add_route' ?>">
    <?= $editRoute ? 'Update Route' : 'Add Route' ?>
</button>

<?php if($editRoute): ?>
<a href="routes.php" class="btn" style="background:#999;">Cancel</a>
<?php endif; ?>

<?php if($msg): ?>
<p style="color:green;margin-top:10px"><?= $msg ?></p>
<?php endif; ?>

</form>
</div>

</div>

</section>

<!-- ================= ROUTE LIST ================= -->
<h2 style="text-align:center;">Route List</h2>

<table>
<tr>
    <th>Source</th>
    <th>Destination</th>
    <th>Distance</th>
    <th>Action</th>
</tr>

<?php
$q = $conn->query("SELECT * FROM routes ORDER BY id DESC");

if($q->num_rows == 0){
    echo "<tr><td colspan='4' align='center'>No Routes Found</td></tr>";
}

while($r = $q->fetch_assoc()):
?>
<tr>
    <td><?= $r['source'] ?></td>
    <td><?= $r['destination'] ?></td>
    <td><?= $r['distance'] ?></td>
    <td>
        <a href="?edit_route=<?= $r['id'] ?>" class="action-btn btn-edit">
            Edit
        </a>

        <a href="?delete_route=<?= $r['id'] ?>"
           class="action-btn btn-delete"
           onclick="return confirm('Delete this route?')">
            Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>
