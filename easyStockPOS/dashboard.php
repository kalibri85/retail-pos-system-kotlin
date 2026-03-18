<?php
require_once 'init.php';
include 'header.php';
?>
<div class="container mt-5">
    <p>Name: <?= htmlspecialchars($user['name'].' '.$user['surname'] ?? 'Guest') ?></p>
    <p>Username: <?= htmlspecialchars($user['username'] ?? 'Guest') ?></p>
    <p>Email: <?= htmlspecialchars($user['email'] ?? '-') ?></p>
    <p>Role: <?= Auth::getRoleName() ?></p>
</div>

<?php include 'footer.php'; ?>