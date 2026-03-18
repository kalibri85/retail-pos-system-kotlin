<?php
//redirect if not logined
if(!isset($_SESSION['userID']) && basename($_SERVER['PHP_SELF']) != 'index.php'){
    header('Location: index.php');
}
?>
<!DOCTYPE html>    
<html>
    <head>    
        <meta charset="UTF-8">
        <title>EasyStock POS</title>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="css/style.css">
        <!-- Font Awesome -->
        <script src="https://kit.fontawesome.com/814fa22a7d.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-danger-red px-3">
            <a class="navbar-brand" href="dashboard.php">EasyStock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-menu">
                <span class="navbar-toggler-icon"></span>
            </button>    
            <div class="collapse navbar-collapse" id="main-menu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">My account</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['userID'])): ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout <i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>  