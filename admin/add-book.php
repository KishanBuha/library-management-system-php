<?php
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');
include_once('../includes/admin_header.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $copies = intval($_POST['copies'] ?? 1);
    $category = trim($_POST['category'] ?? '');

    if (empty($title) || empty($author) || empty($isbn) || empty($category) || $copies < 1) {
        $message = "<div class='alert alert-danger'>All fields are required and copies must be at least 1.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, copies, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $title, $author, $isbn, $copies, $category);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Book added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add book.</div>";
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Add New Book</h2>
    <?php echo $message; ?>
    <form method="post">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Author</label>
            <input type="text" name="author" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ISBN</label>
            <input type="text" name="isbn" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Copies</label>
            <input type="number" name="copies" class="form-control" min="1" value="1" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Book</button>
        <a href="manage-books.php" class="btn btn-secondary">Back</a>
    </form>
</div>
<?php include_once('../includes/admin_footer.php'); ?>