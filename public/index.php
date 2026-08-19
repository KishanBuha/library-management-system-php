<?php
// Start session and include necessary files
session_start();
require_once('../includes/db_connect.php'); 
require_once('../includes/header.php');

// Check if the student is logged in to display personalized content
$is_logged_in = isset($_SESSION['student_id']);
$student_name = '';
$issued_books_count = 0;

if ($is_logged_in) {
    $student_name = htmlspecialchars($_SESSION['student_name'] ?? 'Student');
    $student_id = $_SESSION['student_id'];

    // Fetch the number of currently issued books for the logged-in student
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM issued_books WHERE student_id = ? AND return_date IS NULL");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $issued_books_count = $result['count'] ?? 0;
    $stmt->close();
}
?>

<div class="container mt-5">
    <?php if ($is_logged_in): ?>
        <!-- Logged-in Student Dashboard -->
        <div class="text-center mb-5">
            <h1 class="display-5">Welcome, <?php echo $student_name; ?>!</h1>
            <p class="lead text-muted">Here's a quick overview of your library activity.</p>
        </div>

        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-book-half fs-1 text-primary"></i>
                        <h5 class="card-title mt-3">Currently Issued Books</h5>
                        <p class="display-4 fw-bold"><?php echo $issued_books_count; ?></p>
                        <a href="my-books.php" class="btn btn-outline-primary mt-auto">View My Books</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-search fs-1 text-success"></i>
                        <h5 class="card-title mt-3">Explore the Collection</h5>
                        <p class="card-text">Find your next read from our extensive catalog.</p>
                        <a href="view-books.php" class="btn btn-success mt-auto">Browse All Books</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-person-circle fs-1 text-info"></i>
                        <h5 class="card-title mt-3">Your Profile</h5>
                        <p class="card-text">Manage your account and contact preferences.</p>
                        <a href="contact.php" class="btn btn-outline-info mt-auto">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Logged-out Welcome Page -->
        
        <div class="text-center">
            <h1 class="mb-4">Welcome to the Library Management System</h1>
            <p class="lead mb-4">
                Easily search, issue, and manage books online. Students can view available books and track their borrowed books.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="login.php" class="btn btn-primary btn-lg">Student Login</a>
                <a href="register.php" class="btn btn-outline-secondary btn-lg">Register</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include the footer file
require_once('../includes/footer.php');
?>
