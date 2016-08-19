<?php

session_start();

if ($_SESSION['user_type'] != 1 && $_SESSION['user_id'] != $_GET['id']) {
    header("Location: users.php");
    exit;
}

require_once('DbConnect.php');
$db = new DbConnect();

if ($db->find_user_by_id($_GET['id'])) {

    $db->prepare('DELETE FROM users WHERE id = :id');
    $db->bindParam(':id', $_GET['id']);
    $db->execute();
    $_SESSION['success'] = 'Successfully deleted.';
    header("Location: users.php");
}

?>