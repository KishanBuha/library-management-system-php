<?php
session_start();
require_once "../includes/db_connect.php";

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

// Fine Calculation Function (Shared with My Books and Admin Return)
function calculateFine($issue_date, $return_date = null) {
    $due_period_days = 15;
    $fine_per_day = 10;
    
    // Use current date if not returned
    $calc_return_date = $return_date ?: date('Y-m-d');

    $issue = new DateTime($issue_date);
    $return = new DateTime($calc_return_date);

    // If the book has not been returned and the issue date is in the future, return 0 (shouldn't happen)
    if ($return < $issue) return 0;

    // Calculate the difference in days between issue and calculated return
    $interval = $issue->diff($return);
    $days_held = $interval->days;

    // Calculate overdue days: subtract the grace period
    $overdue_days = max(0, $days_held - $due_period_days);

    // Calculate total fine
    return $overdue_days * $fine_per_day;
}


// Set the page title for the header
$page_title = "Book History";
require_once('../includes/header.php');

$student_id = $_SESSION['student_id'];

// --- PAGINATION LOGIC START ---
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 1. Get total number of records
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM issued_books WHERE student_id = ?");
$total_stmt->bind_param("i", $student_id);
$total_stmt->execute();
$total_rows = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;
// --- PAGINATION LOGIC END ---

// Fetch ALL issued books for the logged-in student (This is the History)
$stmt = $conn->prepare("
    SELECT b.title, b.author, ib.issue_date, ib.return_date, ib.due_date
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    WHERE ib.student_id = ?
    ORDER BY ib.issue_date DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $student_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">

    <h3 class="text-center mb-4">Your Complete Book History</h3>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Status</th>
                        <th>Fine</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = $offset + 1; while ($row = $result->fetch_assoc()): 
                        // Calculate fine for display. If return_date is set, the fine is final. If not, the fine is calculated up to today.
                        $fine = calculateFine($row['issue_date'], $row['return_date']);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['author'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['issue_date'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['due_date'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (is_null($row['return_date'])): ?>
                                    <span class="badge bg-warning text-dark">Currently Borrowed</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Returned on <?= htmlspecialchars($row['return_date']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (is_null($row['return_date'])): ?>
                                    <?php if ($fine > 0): ?>
                                        <span class="badge bg-danger">₹<?= $fine ?> (Overdue)</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">No Fine Yet</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($fine > 0): ?>
                                        <span class="badge bg-danger">₹<?= $fine ?> Paid*</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">₹0</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <p class="text-end text-muted mt-3">*All fines must be paid via cash to the library administrator.</p>
    <?php else: ?>
        <div class="alert alert-info text-center">You have no book history.</div>
    <?php endif; ?>

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
    </div>

<?php
if (isset($stmt)) $stmt->close();
include_once("../includes/footer.php");
?>