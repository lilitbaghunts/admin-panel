<?php

session_start();

if ($_SESSION['user_type'] != 1) {
    header("Location: categories.php");
    exit;
}

require_once('DbConnect.php');
$db = new DbConnect();

if ($db->find_category_by_id($_GET['id'])) {

    $db->prepare('DELETE FROM categories WHERE id = :id');
    $db->bindParam(':id', $_GET['id']);
    $db->execute();
    $_SESSION['success'] = 'Successfully deleted.';
    header("Location: categories.php");
}

?>