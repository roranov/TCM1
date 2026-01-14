<?php
session_start();

$conn = new mysqli("localhost","root","","transport_db");
if($conn->connect_error){
    die("Database Connection Failed");
}

$error = "";

if(isset($_POST['login'])){
    $u = trim($_POST['username']);
    $p = md5(trim($_POST['password']));

    $q = $conn->query("SELECT * FROM admin WHERE username='$u' AND password='$p'");
    if($q && $q->num_rows === 1){
        $_SESSION['admin'] = $u;
        header("Location: tms.php");
        exit;
    }else{
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="admin-login-page">
    <div class="admin-login-card">

        <h2>Admin Login</h2>

        <form method="POST" autocomplete="off">

            <input type="text"
                   name="username"
                   placeholder="Username"
                   required>

            <input type="password"
                   name="password"
                   placeholder="Password"
                   required>

            <button type="submit" name="login">Login</button>

            <?php if($error): ?>
                <p class="login-error"><?= $error ?></p>
            <?php endif; ?>

        </form>

    </div>
</div>

</body>
</html>
