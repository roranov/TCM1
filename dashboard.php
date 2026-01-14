<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="navbar">
    <div style="color:white;">Admin Dashboard</div>
    <div>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">

<section>
<h2>Transport Management</h2>

<div class="grid">

<div class="card">
<h3>Add Driver</h3>
<form method="POST">
<input placeholder="Driver Name">
<input placeholder="License No">
<input placeholder="Phone">
<button>Add Driver</button>
</form>
</div>

<div class="card">
<h3>Assign Transport</h3>
<form>
<select><option>Select Vehicle</option></select>
<select><option>Select Driver</option></select>
<select><option>Select Route</option></select>
<button>Assign</button>
</form>
</div>

</div>
</section>

</div>

</body>
</html>
