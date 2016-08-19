<?php

session_start();
require_once('DbConnect.php');
$db = new DbConnect();
$article = $db->find_article_by_id($_GET['id']);

if (!$article || ($_SESSION['user_type'] != 1 && $_SESSION['user_id'] != $article['user_id'])) {
    header("Location: articles.php");
    exit;

} else {
    $db->prepare('DELETE FROM articles WHERE id = :id');
    $db->bindParam(':id', $article['id']);
    $db->execute();
    $_SESSION['success'] = 'Successfully deleted.';
    header("Location: articles.php");
}

?>