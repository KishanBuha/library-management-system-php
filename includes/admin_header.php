<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sabargam Library System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="../assets/css/admin_style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">📚 Sabargam Library System</a>
            <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" aria-controls="adminOffcanvas">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-end" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminOffcanvasLabel">Admin Panel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'manage-books.php' || $current_page == 'add-book.php' || $current_page == 'edit-book.php') ? 'active' : ''; ?>" href="manage-books.php"><i class="bi bi-book me-2"></i>Manage Books</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'manage-students.php' || $current_page == 'add-student.php' || $current_page == 'edit-student.php') ? 'active' : ''; ?>" href="manage-students.php"><i class="bi bi-people me-2"></i>Manage Students</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'issue-book.php') ? 'active' : ''; ?>" href="issue-book.php"><i class="bi bi-arrow-right-square me-2"></i>Issue Book</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'return-book.php') ? 'active' : ''; ?>" href="return-book.php"><i class="bi bi-arrow-left-square me-2"></i>Return Book</a></li>
            <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" href="reports.php"><i class="bi bi-file-earmark-text me-2"></i>Reports</a></li>
        </ul>
        <div class="mt-auto"><a class="btn btn-danger w-100" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></div>
    </div>
</div>

<main class="container mt-4">