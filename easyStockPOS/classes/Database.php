<?php
class Database {
    private static $host = 'localhost';
    private static $dbname = 'easyStockPOS';
    private static $username = 'root';
    private static $password = '';
    private static $pdo = null;

    //Prevents creating object
    //Prevents the creation of multiple instances of the Database class
    private function __construct(){}
    //Prevents cloning of the db instances
    private function __clone(){}

    //Initialise PDO connection only once (self:: = static property of this class)
    public static function connect(){
        if(self::$pdo === null){
            try{
                self::$pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbname.";charset=utf8", self::$username, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch(PDOException $e){
                die('Database connection error');
            }
        }

        return self::$pdo;
    }
}
?>