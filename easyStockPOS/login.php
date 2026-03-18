<?php
require_once 'config.php';
require_once 'classes/Database.php';

header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing credentials'
    ]);
    exit;
}

try {
    $pdo = Database::connect();
    //Get User
    $stmt = $pdo->prepare(
        "SELECT id, username, password FROM users WHERE username = ? LIMIT 1"
    );
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Wrong password'
        ]);
        exit;
    }

    // Login successfully
    session_start();
    $_SESSION['user_id'] = $user['id'];

    echo json_encode([
        'success' => true,
        'user_id' => $user['id'],
        'username' => $user['username']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}