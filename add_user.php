<?php

session_start();
require_once("DbConnect.php");

$errors = [];
if (isset($_POST['submit'])) {


    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $password = hash('ripemd160', filter_var(trim($_POST['password'])));
        $type = $_POST['type'];

        $db  = new DbConnect();

        $db->prepare("INSERT INTO users (username, password, type) VALUES (:username, :password, :type)");
        $db->bindParam(':username', $username);
        $db->bindParam(':password', $password);
        $db->bindParam(':type', $type);
        $db->execute();

    } else {
        if (empty($_POST['username'])) {
            $errors['username'] = 'Username cannot be blank.';
            $_SESSION['post']['password'] = $_POST['password'];
        }
        if (empty($_POST['password'])) {
            $errors['password'] = 'Password cannot be blank.';
            $_SESSION['post']['username'] = $_POST['username'];
        }
        $_SESSION['post']['type'] = $_POST['type'];
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
    }
    header("location: users.php");
}

?>

