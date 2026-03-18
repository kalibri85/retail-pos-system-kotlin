<?php
class Auth {
    public static function login($username, $password){
        $pdo = Database::connect();

        $q = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $q->execute(['username' => $username]);
        $user = $q->fetch();

        if($user && password_verify($password, $user['password'])){
            session_regenerate_id(true);
            $_SESSION['userID'] = $user['id'];
            $_SESSION['role'] = (int)$user['role'];
            return true;
        }
        return false;
    }
    //logout
    public static function logout(){
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }
    //Check if user is logged in
    public static function check(){
        return isset($_SESSION['userID']);
    }
    //Check role (is it admin)
    public static function isAdmin(){
        return (isset($_SESSION['role']) && $_SESSION['role'] == 1);
    }
    public static function getUserById($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    public static function getRoleName(){
        if(!isset($_SESSION['role'])) return 'Guest';
        return $_SESSION['role'] == 1 ? 'Admin' : 'Employee';
    }
}
?>