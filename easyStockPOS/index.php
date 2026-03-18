<?php
$requireLogin = false;
require_once 'init.php';

$error = null;

//POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if(Auth::login($username, $password)){
        header('Location: dashboard.php');
        exit;
    } else{
        $error = 'Invalid username or password';
    }
}
include 'header.php';
?>  
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center mb-4">Login</h2>
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div> 
            <?php endif; ?>    
            <!-- Login Form -->
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-danger btn-lg w-100">Login</button>
            </form>     
        </div>    
    </div> 
 </div>  
 
<?php include 'footer.php'; ?>