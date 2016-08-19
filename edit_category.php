<?php

require_once('DbConnect.php');
include('layouts/header.php');

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();
$category = $db->find_category_by_id($_GET['id']);
if (!$category || ($_SESSION['user_type'] != 1)) {
    header("Location: categories.php");
}

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['title']) && !empty($_POST['file_type'])) {

        $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
        $fileTypes = json_encode($_POST['file_type']);
        $db  = new DbConnect();

        $db->prepare("UPDATE categories SET title = :title, file_types = :file_types WHERE id = :id");
        $db->bindParam(':id', $category['id']);
        $db->bindParam(':title', $title);
        $db->bindParam(':file_types', $fileTypes);
        $db->execute();
        $_SESSION['success'] = 'Successfully edited.';
        header("location: categories.php");

    } else {
        if (empty($_POST['title'])) {
            $errors['title'] = 'Title cannot be blank.';

        }


        if (empty($_POST['file_type'])) {
            $errors['file_type'] = 'File types cannot be blank.';

        }
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
    }
}

?>

<form action="edit_category.php?id=<?=$category['id'];?>" method="post">
    <h2>Edit Category</h2>
    <?php if (!empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            echo '<span class="error">' . $error . '</span></br>';
        }
        $_SESSION['errors'] = null;
    }
    ?>
    <div class="padd">
        <label>Title: </label>
        <input type="text" name="title" value="<?=$category['title'];?>"/>
    </div>
    <div class="padd">
        <label>File types: </label>
        <?php $fileTypes = $category['file_types'] ? json_decode($category['file_types']) : []; ?>
        <input type="checkbox" name="file_type[]" value="mp3" <?= in_array('mp3', $fileTypes) ? 'checked' : '' ;?> />.mp3
        <input type="checkbox" name="file_type[]" value="wav" <?= in_array('wav',$fileTypes) ? 'checked' : '' ;?> />.wav
        <input type="checkbox" name="file_type[]" value="jpeg" <?= in_array('jpeg', $fileTypes) ? 'checked' : '' ;?> />.jpeg
        <input type="checkbox" name="file_type[]" value="jpg" <?= in_array('jpg', $fileTypes) ? 'checked' : '' ;?> />.jpg
    </div>

    <div class="padd">
        <input type="submit" name="submit" value="Edit"/>
    </div>
</form>
