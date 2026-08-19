<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $student_id = intval($_POST['student_id']);
    $issue_date = date('Y-m-d');
    // ADDED: Calculate due date (15 days from issue)
    $due_date = date('Y-m-d', strtotime('+15 days', strtotime($issue_date)));

    // --- NEW CHECK: Prevent issuing the same book to the same student twice ---
    $check_duplicate_stmt = $conn->prepare("SELECT id FROM issued_books WHERE book_id = ? AND student_id = ? AND return_date IS NULL LIMIT 1");
    $check_duplicate_stmt->bind_param("ii", $book_id, $student_id);
    $check_duplicate_stmt->execute();
    $duplicate_result = $check_duplicate_stmt->get_result();
    
    if ($duplicate_result->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Error: This student already has an unreturned copy of this book.</div>";
    } else {
        // Check if copies are available
        $book = $conn->query("SELECT copies FROM books WHERE id = $book_id")->fetch_assoc();
        if ($book && $book['copies'] > 0) {
            // Insert into issued_books (MODIFIED to include due_date)
            $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_id, issue_date, due_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $book_id, $student_id, $issue_date, $due_date);
            $stmt->execute();

            // Decrease book copy count
            $conn->query("UPDATE books SET copies = copies - 1 WHERE id = $book_id");

            $message = "<div class='alert alert-success'>Book issued successfully! Due date is: " . htmlspecialchars($due_date) . "</div>";
        } else {
            $message = "<div class='alert alert-danger'>No copies available for this book.</div>";
        }
    }
}

// Fetch books and students for dropdown
$books = $conn->query("SELECT id, title FROM books WHERE copies > 0");
$students = $conn->query("SELECT id, name FROM students");
?>

<div class="container mt-5">
    <h2 class="mb-4">Issue Book</h2>

    <?php echo $message; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="book_id" class="form-label">Select Book</label>
            <select name="book_id" id="book_id" class="form-select" required>
                <option value="">-- Choose Book --</option>
                <?php while ($book = $books->fetch_assoc()): ?>
                    <option value="<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="student_id" class="form-label">Select Student</label>
            <select name="student_id" id="student_id" class="form-select" required aria-label="Select a student">
                <option value="">-- Choose Student --</option>
                <?php if ($students && $students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>">
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="" disabled>No students available</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Issue Book</button>
        </div>
    </form>
</div>

<?php include_once('../includes/admin_footer.php'); ?>