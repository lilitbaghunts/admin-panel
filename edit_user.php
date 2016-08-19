<?php

require_once('DbConnect.php');
include('layouts/header.php');

//  Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();
$user = $db->find_user_by_id($_GET['id']);
//  Redirect if user is not found, or is not admin and not the logged in user
if (!$user || ($_SESSION['user_type'] != 1 && $_SESSION['user_id'] != $user['id'])) {
    header("Location: users.php");
}

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
        $password = hash('ripemd160', filter_var(trim($_POST['password'])));
        $type = $_POST['type'];

        $db  = new DbConnect();

        $db->prepare("UPDATE users SET username = :username, password = :password, type = :type
            WHERE id = :id");
        $db->bindParam(':id', $user['id']);
        $db->bindParam(':username', $username);
        $db->bindParam(':password', $password);
        $db->bindParam(':type', $type);
        $db->execute();
        $_SESSION['success'] = 'Successfully edited.';
        header("location: users.php");

    } else {
        if (empty($_POST['username'])) {
            $errors['username'] = 'Username cannot be blank.';
        }
        if (empty($_POST['password'])) {
            $errors['password'] = 'Password cannot be blank.';
        }
    }
    if ($errors) {
        $_SESSION['errors'] = $errors;
    }

}

?>

<form action="edit_user.php?id=<?=$user['id'];?>" method="post">
    <h2>Edit User</h2>
    <?php if (!empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            echo '<span class="error">'.$error.'</span></br>';
        }
        $_SESSION['errors'] = null;
    }
    ?>
    <div class="padd">
        <label>Username: </label>
        <input type="text" name="username" value="<?=$user['username'];?>" />
    </div>
    <div class="padd">
        <label>Password: </label>
        <input type="password" name="password" value="<?=$user['username'];?>" />
    </div>
    <?php if ($_SESSION['user_id'] != $user['id']) { ?>
        <div class="padd">
            <input type="radio" name="type" value="0" <?=$user['type']==0 ? 'checked' : '';?> />
            User
            <input type="radio" name="type" value="1" <?=$user['type']==1 ? 'checked' : '';?> />
            Admin
        </div>
    <?php } ?>
    <div class="padd">
        <input type="submit" name="submit" value="Edit" />
    </div>
</form>
