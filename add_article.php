<?php

include('layouts/header.php');

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

require_once("DbConnect.php");
$db  = new DbConnect();

$category = $db->find_category_by_id($_GET['cat']);

//  Redirect if category not found
if (!$category) {
    header("Location: categories.php");
}

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['title']) && !empty($_POST['description'])) {

        $newFileName = null;

        if (!empty($_FILES['file']['tmp_name'])) {

                $dir = 'uploaded_files/';
                $newFileName = str_replace(' ', '_', $_FILES['file']['name']);
                $file = $dir . basename($newFileName);

                $imageFileType = pathinfo($file,PATHINFO_EXTENSION);

                $categoryFileTypes = json_decode($category['file_types']);

                if (!in_array($imageFileType, $categoryFileTypes)) {
                    $errors['file_error'] = 'Please upload ' . $category['file_types'] . ' files';
                } else {
                    if ($_SESSION['user_type'] == 1 && ($imageFileType != 'jpg' || $imageFileType != 'jpeg')) {
                        $errors['file_error'] = 'Admins not allowed to upload audio files !!';
                    }
                }
                if (!$errors) {
                    if (!move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
                        $errors['file_error'] = 'Failed to upload a file';
                    }
                }
        }

        $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

        if (empty($errors)) {
            $db->prepare("INSERT INTO articles (title, description, file_name, category_id, user_id)
              VALUES (:title, :description, :file_name, :category_id, :user_id)");
            $db->bindParam(':title', $title);
            $db->bindParam(':description', $description);
            $db->bindParam(':file_name', $newFileName);
            $db->bindParam(':category_id', $_GET['cat']);
            $db->bindParam(':user_id', $_SESSION['user_id']);
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

<form action="add_article.php?cat=<?=$category['id'];?>" method="post" enctype="multipart/form-data">
    <h2>Add Article to <?= $category['title']; ?> category</h2>
    <?php if (!empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            echo '<span class="error">' . $error . '</span></br>';
        }
        $_SESSION['errors'] = null;
    }
    ?>
    <div class="padd">
        <label>Title: </label>
        <input type="text" name="title" />
    </div>
    <div class="padd">
        <label>Description: </label>
        <textarea name="description"></textarea>
    </div>

    <div class="padd">
        <input type="file" name="file" />
    </div>

    <div class="padd">
        <input type="submit" name="submit" value="Add"/>
    </div>
</form>

