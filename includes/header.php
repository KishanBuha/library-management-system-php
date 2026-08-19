<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Library Management System'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">📚 Sabargam Library System</a>
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Sabargam Library</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php"><i class="bi bi-house-door me-2"></i>Home</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'view-books.php') ? 'active' : ''; ?>" href="view-books.php"><i class="bi bi-book me-2"></i>View Books</a></li>
            <?php if (isset($_SESSION['student_id'])): ?>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'my-books.php') ? 'active' : ''; ?>" href="my-books.php"><i class="bi bi-collection me-2"></i>My Books</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>" href="history.php"><i class="bi bi-clock-history me-2"></i>Book History</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php"><i class="bi bi-envelope me-2"></i>Contact</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php"><i class="bi bi-envelope me-2"></i>Contact</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="login.php"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'register.php') ? 'active' : ''; ?>" href="register.php"><i class="bi bi-person-plus me-2"></i>Register</a></li>
            <?php endif; ?>
        </ul>

        <?php if (isset($_SESSION['student_id'])): ?>
            <div class="mt-auto"><a class="btn btn-danger w-100" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></div>
        <?php endif; ?>
    </div>
</div>

<main class="container py-4">