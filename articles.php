<?php

require_once("DbConnect.php");
include("layouts/header.php");

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();
$db->prepare('SELECT a.title AS a_title, a.id AS a_id,a.description AS a_description,
c.title AS c_title, a.file_name AS a_file, c.file_types, c.id AS c_id, user_id
FROM articles AS a LEFT JOIN categories AS c ON (a.category_id = c.id) ORDER BY a.id');
$articles = $db->fetchAll();

if ($articles) { ?>
    <div class="pull-left">
        <h2>Articles</h2>
        <?php if (!empty($_SESSION['success'])) {
            echo '<span class="error">'.$_SESSION['success'].'</span></br>';
            $_SESSION['success'] = null;
        }
        ?>
        <table>
            <thead>
            <tr>
                <td>Title</td>
                <td>Description</td>
                <td>Category</td>
                <td>File</td>
                <td colspan="2">Actions</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($articles as $article) { ?>
            <tr>
                <td><?= $article['a_title']; ?></td>
                <td><?= $article['a_description']; ?></td>
                <td><?= $article['c_title']; ?></td>
                <td>
                    <?php if (!empty($article['a_file'])) {
                        if (array_intersect(['mp3', 'wav'], json_decode($article['file_types']))) { ?>
                            <audio controls>
                                <source src="uploaded_files/<?= $article['a_file']; ?>" type="audio/mpeg">
                            </audio>
                        <?php } elseif (array_intersect(['jpg', 'jpeg'], json_decode($article['file_types']))) {
                            if (file_exists('uploaded_files/' . $article['a_file'])) { ?>
                                <img src="uploaded_files/<?= $article['a_file']; ?>" width="150px"/>
                            <?php }
                            }
                        }
                    ?>
                    </td>
                    <?php if ($_SESSION['user_type'] == 1 || $_SESSION['user_id'] == $article['user_id']) { ?>
                        <td><a href="edit_article.php?id=<?=urlencode($article['a_id']);?>">Edit<a></a></td>
                        <td><a href="delete_article.php?id=<?=urlencode($article['a_id']);?>">Delete</a></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php }  else { ?>
    No Articles.
<?php } ?>

