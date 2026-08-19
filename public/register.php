<?php
session_start();
require_once "../includes/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = password_hash(trim($_POST['password'] ?? ''), PASSWORD_DEFAULT);

    if (empty($name) || empty($student_id) || empty($roll_no) || empty($department) || empty($email) || empty($_POST['password'])) {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? OR email = ?");
        $stmt->bind_param("ss", $student_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Student ID or Email already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (name, student_id, roll_no, department, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $student_id, $roll_no, $department, $email, $password);

            if ($stmt->execute()) {
                header("Location: login.php?status=success");
                exit;
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - Library Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">

<div class="container">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-header text-center">
            <h4>Student Registration</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                 <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" name="student_id" id="student_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="roll_no" class="form-label">Roll No</label>
                    <input type="text" name="roll_no" id="roll_no" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" name="department" id="department" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email ID</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Create Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                Already registered? <a href="login.php">Login here</a>.
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
