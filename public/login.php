<?php
ob_start();
session_start();
require_once "../includes/db_connect.php";
$page_title = "Student Login"; // Set page title
require_once('../includes/header.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id_input = trim($_POST['student_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($student_id_input) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, student_id, password FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $student = $result->fetch_assoc();

            if (password_verify($password, $student['password'])) {
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                $_SESSION['student_logged_in'] = true;

                // FIX: Redirect to the home page (index.php) after login
                header("Location: index.php");
                exit;

            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Student not found.";
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 450px;">
        <div class="card-header text-center">
            <h4>Student Login</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</div>
<?php require_once('../includes/footer.php'); ?>
<?php ob_end_flush(); ?>
