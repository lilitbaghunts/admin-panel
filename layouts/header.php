<?php
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">
    </head>

    <body>

    <ul>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <li class="pull-left"><a href="categories.php">Categories</a></li>
            <li class="pull-left"><a href="articles.php">Articles</a></li>
            <li class="pull-left"><a href="users.php">Users</a></li>
            <li class="pull-right">
                <a href="logout.php">Logout</a>
                <a href="edit_user.php?id=<?=urlencode($_SESSION['user_id']);?>">[<?= $_SESSION['username'] ?>]</a>

            </li>

        <?php } ?>
    </ul>


