<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

$message = "";

// ADDED: Fine Calculation Function
function calculateFine($issue_date, $return_date) {
    $due_period_days = 15;
    $fine_per_day = 10;

    $issue = new DateTime($issue_date);
    $return = new DateTime($return_date);

    // Calculate the difference in days between issue and return
    $interval = $issue->diff($return);
    $days_held = $interval->days;

    // Calculate overdue days
    // Fine is applied for days held > 15
    $overdue_days = max(0, $days_held - $due_period_days);

    // Calculate total fine
    return $overdue_days * $fine_per_day;
}

// --- NEW TWO-STEP LOGIC ---
$action_content = ""; // Content for the confirmation form/message
$issue_id_to_show = 0; // The ID currently being processed for confirmation

// 1. Handle Final Return (Cash Collected Confirmed)
if (isset($_GET['finalize_return_id'])) {
    $issue_id = intval($_GET['finalize_return_id']);
    $return_date = date('Y-m-d');

    // Get book_id and issue_date to calculate final fine and increase copy count
    $stmt = $conn->prepare("SELECT book_id, issue_date FROM issued_books WHERE id = ? AND return_date IS NULL");
    $stmt->bind_param("i", $issue_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $book_id = $data['book_id'];
        $issue_date = $data['issue_date'];

        $fine = calculateFine($issue_date, $return_date);

        // START TRANSACTION for atomicity
        $conn->begin_transaction();
        try {
            // A. Update return date
            $stmt_update = $conn->prepare("UPDATE issued_books SET return_date = ? WHERE id = ?");
            $stmt_update->bind_param("si", $return_date, $issue_id);
            $stmt_update->execute();

            // B. Increase book copy count
            $stmt_copies = $conn->prepare("UPDATE books SET copies = copies + 1 WHERE id = ?");
            $stmt_copies->bind_param("i", $book_id);
            $stmt_copies->execute();

            $conn->commit();
            
            // Success message
            if ($fine > 0) {
                $issue_dt = new DateTime($issue_date);
                $return_dt = new DateTime($return_date);
                $days_held = $issue_dt->diff($return_dt)->days;
                $overdue_days_display = max(0, $days_held - 15);
                $message = "<div class='alert alert-success'>Book finalized and returned. Fine of <strong>₹{$fine}</strong> collected via cash (overdue by {$overdue_days_display} days).</div>";
            } else {
                 $message = "<div class='alert alert-success'>Book finalized and returned. No fine applied.</div>";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $message = "<div class='alert alert-danger'>Failed to finalize return. Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid or already returned entry.</div>";
    }
}
// 2. Handle Initial Check/Fine Calculation (Pre-Return Confirmation)
elseif (isset($_GET['check_fine_id'])) {
    $issue_id_to_show = intval($_GET['check_fine_id']);

    // Get book/student details for confirmation display
    $stmt = $conn->prepare("
        SELECT ib.issue_date, s.name AS student_name, b.title AS book_title
        FROM issued_books ib
        JOIN students s ON ib.student_id = s.id
        JOIN books b ON ib.book_id = b.id
        WHERE ib.id = ? AND ib.return_date IS NULL
    ");
    $stmt->bind_param("i", $issue_id_to_show);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $issue_date = $data['issue_date'];
        $fine = calculateFine($issue_date, date('Y-m-d')); // Calculate fine up to today

        $action_content = "
            <div class='card mb-4 border-info'>
                <div class='card-header table-dark'>
                    <h5 class='mb-0'>Return Confirmation & Fine Check</h5>
                </div>
                <div class='card-body'>
                    <p><strong>Student:</strong> " . htmlspecialchars($data['student_name']) . "</p>
                    <p><strong>Book:</strong> " . htmlspecialchars($data['book_title']) . "</p>
                    <p><strong>Issue Date:</strong> " . htmlspecialchars($issue_date) . "</p>
                    <p><strong>Calculated Fine (as of today):</strong> 
                        <span class='fs-4 text-danger'>₹{$fine}</span>
                    </p>
                    <hr>
                    <p class='fw-bold'>ACTION REQUIRED:</p>
                    <p>Please confirm that the fine amount of 
                    <span class='text-danger'>₹{$fine}</span> has been collected in cash from the student.</p>
                    
                    <a href='?finalize_return_id={$issue_id_to_show}' class='btn btn-success me-2' onclick=\"return confirm('Confirm cash payment of ₹{$fine} and proceed with the return?');\">
                        <i class='bi bi-check-circle me-1'></i> Confirm Cash & Finalize Return
                    </a>
                    <a href='return-book.php' class='btn btn-secondary'>
                        <i class='bi bi-x-circle me-1'></i> Cancel
                    </a>
                </div>
            </div>
        ";
    } else {
        $message = "<div class='alert alert-danger'>Cannot check fine. The book is either already returned or the record is invalid.</div>";
    }
}

// --- PAGINATION LOGIC START ---
$limit = 5; // Number of records to show per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 1. Get total number of records (Unreturned books)
$total_result = $conn->query("SELECT COUNT(*) AS total FROM issued_books WHERE return_date IS NULL");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Ensure page number is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;

// Fetch issued books (not yet returned) - MODIFIED to apply LIMIT/OFFSET
$issued_query = "
    SELECT 
        issued_books.id, 
        students.name AS student_name, 
        books.title AS book_title, 
        issued_books.issue_date,
        issued_books.due_date 
    FROM issued_books
    JOIN students ON issued_books.student_id = students.id
    JOIN books ON issued_books.book_id = books.id
    WHERE issued_books.return_date IS NULL
    ORDER BY issued_books.issue_date DESC
    LIMIT $limit OFFSET $offset
";
$issued = $conn->query($issued_query);
// --- PAGINATION LOGIC END ---

$today = date('Y-m-d');
?>

<div class="container mt-5">
    <h2 class="mb-4">Return Issued Books</h2>
    <?php echo $message; ?>
    
    <?php echo $action_content; ?>

    <?php if (!$issue_id_to_show): // Only show table if not in confirmation mode ?>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Book Title</th>
                <th>Issue Date</th>
                <th>Due Date</th> 
                <th>Current Fine</th> 
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($issued && $issued->num_rows > 0): ?>
                <?php $i = $offset + 1; while ($row = $issued->fetch_assoc()): 
                    $fine = calculateFine($row['issue_date'], $today);
                    // ADDED: Check for invalid date (0000-00-00)
                    $display_due_date = ($row['due_date'] == '0000-00-00' || empty($row['due_date'])) ? 'N/A (Old Record)' : htmlspecialchars($row['due_date']);
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                        <td><?php echo $display_due_date; ?></td> <td>
                            <?php if ($fine > 0): ?>
                                <span class="badge bg-danger">₹<?php echo $fine; ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">₹0</span>
                            <?php endif; ?>
                        </td> 
                        <td>
                            <a href="?check_fine_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Return</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No issued books to return.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);

                if ($start > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($start > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                for ($i_link = $start; $i_link <= $end; $i_link++) {
                    echo '<li class="page-item ' . (($i_link == $page) ? 'active' : '') . '"><a class="page-link" href="?page=' . $i_link . '">' . $i_link . '</a></li>';
                }

                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                ?>
                
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include_once('../includes/admin_footer.php'); ?>