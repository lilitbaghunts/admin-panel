<?php

require_once('DbConnect.php');
include('layouts/header.php');

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();

$article = $db->find_article_by_id($_GET['id']);

if (!$article) {
    header("Location: articles.php");
}

$category = $db->find_category_by_id($article['category_id']);

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['title']) && !empty($_POST['description'])) {

        $newFileName = $article['file_name'];

        if (!empty($_FILES['file']['tmp_name'])) {

            $dir = 'uploaded_files/';
            $newFileName = uniqid() . str_replace(' ', '_', $_FILES['file']['name']);
            $file = $dir . basename($newFileName);

            $imageFileType = pathinfo($file,PATHINFO_EXTENSION);


            $categoryFileTypes = json_decode($category['file_types']);

            if (!in_array($imageFileType, $categoryFileTypes)) {
                $errors['file_error'] = 'Please upload ' . $category['file_types'] . ' files';
            } else {

                if ($_SESSION['user_type'] == 1 && $imageFileType != 'jpg' && $imageFileType != 'jpeg') {
                    $errors['file_error'] = 'Admins not allowed to upload audio files !!';
                }
            }
            if (!$errors) {
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                    $errors['file_error'] = 'Failed to upload a file';
                } else {
                    unlink('uploaded_files/'.$article['file_name']);
                }
            }
        }

        $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

        if (empty($errors)) {
            $db->prepare("UPDATE articles SET title=:title, description=:description, file_name=:file_name,
              category_id=:category_id, user_id=:user_id WHERE id=:id");
            $db->bindParam(':title', $title);
            $db->bindParam(':description', $description);
            $db->bindParam(':file_name', $newFileName);
            $db->bindParam(':category_id', $article['category_id']);
            $db->bindParam(':user_id', $_SESSION['user_id']);
            $db->bindParam(':id', $article['id']);
            $db->execute();
            $_SESSION['success'] = 'Successfully added.';
            header("location: articles.php");
        }

    } else {

        if (empty($_POST['title'])) {
            $errors['title'] = 'Title cannot be blank.';
            $_SESSION['post']['description'] = $_POST['description'];
        }

        if (empty($_POST['description'])) {
            $errors['description'] = 'Description cannot be blank.';
            $_SESSION['post']['title'] = $_POST['title'];
        }
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
    }

}
?>

<form action="edit_article.php?id=<?=$article['id'];?>" method="post" enctype="multipart/form-data">
    <h2>Edit Article</h2>
    <?php if (!empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            echo '<span class="error">' . $error . '</span></br>';
        }
        $_SESSION['errors'] = null;
    }
    ?>
    <div class="padd">
        <label>Title: </label>
        <input type="text" name="title" value = "<?=$article['title'];?>"/>
    </div>
    <div class="padd">
        <label>Description: </label>
        <textarea name="description"><?=$article['description'];?></textarea>
    </div>

    <div class="padd">
        <?php if (!empty($article['file_name'])) {
            if (array_intersect(['mp3', 'wav'], json_decode($category['file_types']))) { ?>
                <audio controls>
                    <source src="uploaded_files/<?= $article['file_name']; ?>" type="audio/mpeg">
                </audio>
            <?php } elseif (array_intersect(['jpg', 'jpeg'], json_decode($article['file_types']))) {
                if (file_exists('uploaded_files/' . $category['file_name'])) { ?>
                    <img src="uploaded_files/<?= $article['file_name']; ?>" width="150px"/>
                <?php }
            }
        }
        ?>
        <input type="file" name="file" />
    </div>

    <div class="padd">
        <input type="submit" name="submit" value="Edit"/>
    </div>
</form>
