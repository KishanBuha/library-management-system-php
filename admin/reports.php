<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

// --- PAGINATION LOGIC START ---
$limit = 5; // Number of records to show per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 1. Get total number of records
$total_result = $conn->query("SELECT COUNT(*) AS total FROM issued_books");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Ensure page number is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;

// 2. Fetch records for the current page (MODIFIED to include LIMIT/OFFSET)
$query = "
    SELECT 
        issued_books.id,
        students.name AS student_name,
        books.title AS book_title,
        issued_books.issue_date,
        issued_books.return_date
    FROM issued_books
    JOIN students ON issued_books.student_id = students.id
    JOIN books ON issued_books.book_id = books.id
    ORDER BY issued_books.issue_date DESC
    LIMIT $limit OFFSET $offset
";
$records = $conn->query($query);
// --- PAGINATION LOGIC END ---
?>

<div class="container mt-5">
    <h2 class="mb-4">Issued & Returned Books Report</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Book Title</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($records && $records->num_rows > 0): ?>
                <?php $i = $offset + 1; while ($row = $records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                        <td><?php echo $row['return_date'] ? htmlspecialchars($row['return_date']) : '—'; ?></td>
                        <td>
                            <?php if ($row['return_date']): ?>
                                <span class="badge bg-success">Returned</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Issued</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No records found.</td>
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
    </div>

<?php include_once('../includes/admin_footer.php'); ?>