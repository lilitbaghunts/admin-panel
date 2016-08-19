<?php

require_once("DbConnect.php");
include("layouts/header.php");

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();
$db->prepare('SELECT * FROM categories ORDER BY id');
$categories = $db->fetchAll();

if ($categories) { ?>
    <div class="pull-left">
        <h2>Categories</h2>
        <?php if (!empty($_SESSION['success'])) {
            echo '<span class="error">'.$_SESSION['success'].'</span></br>';
            $_SESSION['success'] = null;
        }
        ?>
        <table>
            <thead>
            <tr>
                <td>Title</td>
                <td>File types</td>
                <td colspan="3">Actions</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category) { ?>
                <tr>
                    <td><?= $category['title']; ?></td>
                    <td><?= $category['file_types']; ?></td>
                    <td><a href="add_article.php?cat=<?=urlencode($category['id']);?>">Add Article</a></td>
                    <?php if ($_SESSION['user_type'] == 1) { ?>
                        <td><a href="edit_category.php?id=<?=urlencode($category['id']);?>">Edit<a></a></td>
                        <td><a href="delete_category.php?id=<?=urlencode($category['id']);?>">Delete</a></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php }  else { ?>
    No Categories.
<?php } ?>

<?php if ($_SESSION['user_type'] == 1) { ?>
    <form action="add_category.php" method="post" class="pull-right">
        <h2>Add Category</h2>
        <?php if (!empty($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $error) {
                echo '<span class="error">' . $error . '</span></br>';
            }
            $_SESSION['errors'] = null;
        }
        ?>
        <div class="padd">
            <label>Title: </label>
            <input type="text" name="title" value="<?= isset($_SESSION['post']['title']) ? $_SESSION['post']['title'] : ''; ?>" />
        </div>

        <div class="padd">
            <label>File types: </label>
            <input type="checkbox" name="file_type[]" value="mp3" <?= isset($_SESSION['post']['file_types']) && in_array('mp3', $_SESSION['post']['file_types']) ? 'checked' : '' ;?> />.mp3
            <input type="checkbox" name="file_type[]" value="wav" <?= isset($_SESSION['post']['file_types']) && in_array('wav', $_SESSION['post']['file_types']) ? 'checked' : '' ;?> />.wav
            <input type="checkbox" name="file_type[]" value="jpeg" <?= isset($_SESSION['post']['file_types']) && in_array('jpeg', $_SESSION['post']['file_types']) ? 'checked' : '' ;?> />.jpeg
            <input type="checkbox" name="file_type[]" value="jpg" <?= isset($_SESSION['post']['file_types']) && in_array('jpg', $_SESSION['post']['file_types']) ? 'checked' : '' ;?> />.jpg

        </div>

        <div class="padd">
            <input type="submit" name="submit" value="Add"/>
        </div>
    </form>
    <?php $_SESSION['post'] = null;
} ?>