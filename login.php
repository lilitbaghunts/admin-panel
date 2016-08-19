<?php
include('layouts/header.php');

if (isset($_SESSION['user_id'])) {
    header('location: admin_panel.php');
}

require_once('DbConnect.php');

$errors = [];
if (isset($_POST['submit'])) {

    if (!empty($_POST['username']) && !empty($_POST['password'])) {

        $username = trim($_POST['username']);//admin
        $password = hash('ripemd160', trim($_POST['password']));//0000

        $db  = new DbConnect();
        $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $db->bindParam(':username', $username);
        $db->bindParam(':password', $password);
        $row = $db->fetchOne();

        if ($row) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_type'] = $row['type'];
            header("location: admin_panel.php");
        } else {
            $errors[] = 'Invalid username or password.';
        }
    } else {
        $errors['username'] = 'Username cannot be blank.';
        $errors['password'] = 'Password cannot be blank.';
    }
}
?>


<h2 class="padd">Login</h2>

<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<span class="error">'.$error.'</span></br>';
    }
}
?>
<form action="login.php" method="post">
    <div class="padd">
        <label>Username: </label>
        <input type="text" name="username" />
    </div>
    <div class="padd">
        <label>Password: </label>
        <input type="password" name="password" />
    </div>
    <div class="padd">
        <input type="submit" name="submit" value="Login" />
    </div>
</form>