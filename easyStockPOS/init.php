<?php
    require_once 'config.php';

    $requireLogin = $requireLogin ?? true;

    //Check if user is logged in
    if($requireLogin && !Auth::check()){
        header('Location: index.php');
        exit;
    }
    $user = null;
    if(Auth::check()){
        $user = Auth::getUserById($_SESSION['userID'] ?? null);
    }
?>