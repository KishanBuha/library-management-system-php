<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

// Fetch total books
$total_books_query = $conn->query("SELECT COUNT(*) AS total FROM books");
$total_books = $total_books_query ? $total_books_query->fetch_assoc()['total'] : 0;

// Fetch total students
$total_students_query = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_students = $total_students_query ? $total_students_query->fetch_assoc()['total'] : 0;

// Fetch total issued books (currently not returned)
$total_issued_query = $conn->query("SELECT COUNT(*) AS total FROM issued_books WHERE return_date IS NULL");
$total_issued = $total_issued_query ? $total_issued_query->fetch_assoc()['total'] : 0;

// Fetch total returned books
$total_returned_query = $conn->query("SELECT COUNT(*) AS total FROM issued_books WHERE return_date IS NOT NULL");
$total_returned = $total_returned_query ? $total_returned_query->fetch_assoc()['total'] : 0;
?>

<div class="container">
    <h2 class="text-center my-4"><span class="title-underline">Admin Dashboard</span></h2>
    <!-- Statistics Section -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <p class="card-text display-4"><?php echo htmlspecialchars($total_books ?? '0'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <p class="card-text display-4"><?php echo htmlspecialchars($total_students ?? '0'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">Books Issued</h5>
                    <p class="card-text display-4"><?php echo htmlspecialchars($total_issued ?? '0'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Books Returned</h5>
                    <p class="card-text display-4"><?php echo htmlspecialchars($total_returned ?? '0'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <hr class="my-4">
    <h3 class="text-center mb-4"><span class="title-underline">Quick Actions</span></h3>
    <div class="row text-center g-4">
        <div class="col-lg-2 col-md-4">
            <a href="manage-books.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-journal-album fs-1"></i>
                        <h5 class="card-title mt-3">Manage Books</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4">
            <a href="manage-students.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-people-fill fs-1"></i>
                        <h5 class="card-title mt-3">Manage Students</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4">
            <a href="issue-book.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-box-arrow-up-right fs-1"></i>
                        <h5 class="card-title mt-3">Issue a Book</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4">
            <a href="return-book.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-box-arrow-in-down-left fs-1"></i>
                        <h5 class="card-title mt-3">Return a Book</h5>
                    </div>
                </div>
            </a>
        </div>
         <div class="col-lg-2 col-md-4">
            <a href="add-book.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-plus-circle-dotted fs-1"></i>
                        <h5 class="card-title mt-3">Add New Book</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4">
            <a href="reports.php" class="d-block card-link">
                <div class="card h-100 shadow-sm action-card">
                    <div class="card-body">
                        <i class="bi bi-clipboard-data fs-1"></i>
                        <h5 class="card-title mt-3">View Reports</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
include_once('../includes/admin_footer.php');
?>

