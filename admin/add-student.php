<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

$message = "";

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Retrieve and sanitize input
    $name = trim($_POST['name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 2. Validate input
    if (empty($name) || empty($student_id) || empty($email) || empty($password) || empty($roll_no) || empty($department)) {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Invalid email format.</div>";
    } else {
        // 3. Check for uniqueness (student_id and email)
        $stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
        $stmt->bind_param("ss", $student_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>A student with this ID or Email already exists.</div>";
        } else {
            // 4. Hash the password and insert into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO students (name, student_id, roll_no, department, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssss", $name, $student_id, $roll_no, $department, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $message = "<div class='alert alert-success'>Student added successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Failed to add student. Please try again.</div>";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Add New Student</h2>
    
    <?php echo $message; ?>

    <form action="add-student.php" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="student_id" class="form-label">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="roll_no" class="form-label">Roll Number</label>
                <input type="text" class="form-control" id="roll_no" name="roll_no" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="manage-students.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<?php include_once('../includes/admin_footer.php'); ?>