<?php
// Use require_once for critical files like DB connection.
require_once('../includes/db_connect.php'); 
require_once('../includes/header.php');

// --- PAGINATION LOGIC START ---
$limit = 5; // Number of records to show per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 1. Get total number of records (only books with copies > 0)
$total_result = $conn->query("SELECT COUNT(*) AS total FROM books WHERE copies > 0");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Ensure page number is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;
// --- PAGINATION LOGIC END ---

// Use a prepared statement to prevent SQL injection and include LIMIT/OFFSET.
$sql = "SELECT id, title, author, isbn, category, copies FROM books WHERE copies > 0 ORDER BY title ASC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset); 
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Available Books</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Author</th>
                            <th scope="col">Category</th>
                            <th scope="col">Available Copies</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $i = $offset + 1; while ($book = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['category']); ?></td>
                                    <td>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($book['copies']); ?></span>
                                    </td>
                                    <td>
                                        <?php if (isset($_SESSION['student_id'])): ?>
                                            <span class="text-muted">Contact Admin to Issue</span>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-sm btn-outline-primary">Login / Contact Admin</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No books are currently available.</td>
                            </tr>
                        <?php endif; ?>
                        <?php if (isset($stmt)) $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
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

<?php require_once('../includes/footer.php'); ?>