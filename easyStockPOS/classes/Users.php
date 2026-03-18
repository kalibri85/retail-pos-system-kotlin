<?php
class Users {
    public static function getAllUsers(){
        $pdo = Database::connect();
        $sql = $pdo->query("SELECT * FROM users ORDER BY name ASC");
        return $sql->fetchAll();
    }
    public static function deleteUser($id){
        $pdo = Database::connect();
        $sql = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $sql->execute([$id]);
    }
    public static function createUsers($username, $name, $surname, $email, $password, $role=0){
        $pdo = Database::connect();
        if(self::usernameExists($username)){
            throw new Exception("Username '$username' already exists!");
        }
        $passwordHash =password_hash($password, PASSWORD_DEFAULT);
        $sql = $pdo->prepare("INSERT INTO users (username, name, surname, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        return $sql->execute([$username, $name, $surname, $email, $passwordHash, $role]);
    }
    public static function generatedPassword($length = 14){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $maxIn = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++){
            $password .=$chars[random_int(0, $maxIn)];
        }
        return $password;
    }
    public static function usernameExists($username){
        $pdo = Database::connect();
        $sql = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $sql->execute([$username]);
        return $sql->fetchColumn() > 0;
    }
}
?>