<?php
session_start();
include_once('../includes/db_connect.php');

$error = "";
$show_register = isset($_GET['register']);

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $error = "Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed.";
            }
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $show_register ? 'Admin Registration' : 'Admin Login' ?> - Library Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin_style.css" rel="stylesheet">
</head>
<body class="login-page">

<div class="container">
    <div class="card mx-auto shadow" style="max-width: 450px;">
        <div class="card-header text-center">
            <h4><?= $show_register ? 'Admin Registration' : 'Admin Login' ?></h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($show_register): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                </form>
                <div class="text-center mt-3">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            <?php else: ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                 <div class="text-center mt-3">
                    Need an admin account? <a href="login.php?register=1">Register here</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
