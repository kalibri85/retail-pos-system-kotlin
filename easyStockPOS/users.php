<?php 
require_once 'init.php';
include 'header.php';
$users = Users::getAllUsers();
$generatedPassword = Users::generatedPassword();
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['save'])){
        $username = $_POST['username'] ?? null;
        $name = $_POST['name'] ?? null;
        $surname = $_POST['surname'] ?? null;
        $email = $_POST['mail'] ?? null;
        $password = $_POST['password'] ?? null;
        if($username && $name && $email && $password){
            try{
                Users::createUsers($username, $name, $surname, $email, $password);
                $message = "User '$username' created successfully!";
                $users = Users::getAllUsers();
            } catch (Exception $e){
                $errorMessage = $e->getMessage();
            }
           
        }  
    }
    if(isset($_POST['delete'])){
        $id = $_POST['delete'];
        Users::deleteUser($id);
        $users = Users::getAllUsers();
    }  
}    
?>
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mt-0">Users</h2>
        <button class="btn btn-danger mb-2" id="show-form">Add Employee</button>
    </div>           
    <div id="form-box" class="card p-3 mb-3 d-none mx-auto" style="width: 100%; max-width:600px;">
        <h2 class="mt-0">New User</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Surname</label>
                <input type="text" name="surname" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="mail" class="form-control form-control-lg" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password(Don't forget to copy the password!)</label>
                <input type="text" class="form-control form-control-lg" value="<?= htmlspecialchars($generatedPassword) ?>" readonly>
                <input type="hidden" name="password" value="<?= htmlspecialchars($generatedPassword) ?>">
            </div>
            <div class="text-end">
                <button name="save" class="btn btn-danger">Save</button>
            </div>    
        </form>       
    </div> 
</div>   
    <div class="col-md-12">
        <?php if(!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>   
        <?php if(!empty($errorMessage)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>   
        <?php foreach($users as $user): ?>  
            <div class="row align-items-center border-bottom py-2 text-nowrap">
                <div class="col-sm-1"></div>
                <div class="col-sm-3"><?= htmlspecialchars($user['username']) ?></div>
                <div class="col-sm-2"><?= htmlspecialchars($user['name'] ?? '') ?></div> 
                <div class="col-sm-2"><?= htmlspecialchars($user['surname'] ?? '') ?></div> 
                <div class="col-sm-2"><?= htmlspecialchars($user['email']) ?></div>      
                <div class="col-sm-2">
                    <?php if ($user['role'] == 0): ?>
                        <form method="post">
                            <button type="submit" name="delete" value="<?= (int)htmlspecialchars($user['id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete {$username} user?')">
                                Delete
                            </button>
                        </form>
                    <?php endif; ?>     
                </div>       
            </div> 
        <?php endforeach; ?>    
    </div>
</div>
<script>
    document.getElementById('show-form').addEventListener('click', () =>{
        document.getElementById('form-box').classList.toggle('d-none');
    });
</script>    
<?php include 'footer.php'; ?>