<?php

session_start();
require_once("DbConnect.php");

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['title']) && !empty($_POST['file_type'])) {

        $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
        $fileTypes = json_encode($_POST['file_type']);

        $db  = new DbConnect();

        $db->prepare("INSERT INTO categories (title, file_types) VALUES (:title, :file_types)");
        $db->bindParam(':title', $title);
        $db->bindParam(':file_types', $fileTypes);
        $db->execute();

    } else {

        if (empty($_POST['title'])) {
            $errors['title'] = 'Title cannot be blank.';
            $_SESSION['post']['file_types'] = $_POST['file_type'];
        }


        if (empty($_POST['file_type'])) {
            $errors['file_type'] = 'File types cannot be blank.';
            $_SESSION['post']['title'] = $_POST['title'];
            $_SESSION['post']['file_types'] = [];
        }
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
    }

    header("location: categories.php");

}

?>

