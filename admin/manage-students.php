<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

// --- PAGINATION LOGIC START ---
$limit = 5; // Number of records to show per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 1. Get total number of records
$total_result = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Ensure page number is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;

// 2. Fetch students for the current page
$students_query = "SELECT * FROM students ORDER BY id DESC LIMIT $limit OFFSET $offset";
$students = $conn->query($students_query);

// --- PAGINATION LOGIC END ---


// Handle delete request
if (isset($_GET['delete'])) {
    $student_id = intval($_GET['delete']);
    // Re-fetch the current page to maintain context after deletion
    $redirect_page = isset($_GET['current_page']) ? intval($_GET['current_page']) : 1; 
    
    // Check if the current page would become empty after deletion
    if ($students->num_rows == 1 && $redirect_page > 1) {
         $redirect_page--;
    }

    $conn->query("DELETE FROM students WHERE id = $student_id");
    
    // Redirect back to the potentially adjusted page
    header("Location: manage-students.php?page=$redirect_page");
    exit();
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Manage Students</h2>
    <a href="add-student.php" class="btn btn-primary mb-3">Add New Student</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Roll No</th>
                <th scope="col">Email</th>
                <th scope="col">Department</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($students) && $students->num_rows > 0): ?>
                <?php $i = $offset + 1; while ($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['roll_no'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['department'] ?? ''); ?></td>
                        <td>
                            <a href="edit-student.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manage-students.php?delete=<?php echo $row['id']; ?>&current_page=<?php echo $page; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No students found.</td>
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