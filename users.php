
<?php

require_once("DbConnect.php");
include("layouts/header.php");

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
}

$db = new DbConnect();
$db->prepare("SELECT * FROM users WHERE id <> {$_SESSION['user_id']} ORDER BY id");
$users = $db->fetchAll();

if ($users) { ?>
    <div class="pull-left">
        <h2>Users</h2>
        <?php if (!empty($_SESSION['success'])) {
                echo '<span class="error">'.$_SESSION['success'].'</span></br>';
                $_SESSION['success'] = null;
            }
        ?>
        <table>
            <thead>
            <tr>
                <td>Username</td>
                <td>Type</td>
<!--                <td colspan="2">Actions</td>-->
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?= $user['username']; ?></td>
                    <td><?= $user['type']==1?'admin':'user'; ?></td>
                    <?php if ($_SESSION['user_type'] == 1 || $user['id'] == $_SESSION['user_id']) { ?>
                        <td><a href="edit_user.php?id=<?=urlencode($user['id']);?>">Edit<a></a></td>
                        <td><a href="delete_user.php?id=<?=urlencode($user['id']);?>">Delete</a></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php if ($_SESSION['user_type'] == 1) { ?>
    <form action="add_user.php" method="post" class="pull-right">
        <h2>Add User</h2>
        <?php if (!empty($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $error) {
                echo '<span class="error">' . $error . '</span></br>';
            }
            $_SESSION['errors'] = null;
        }
        ?>
        <div class="padd">
            <label>Username: </label>
            <input type="text" name="username"
                   value="<?= isset($_SESSION['post']['username']) ? $_SESSION['post']['username'] : ''; ?>" />
        </div>
        <div class="padd">
            <label>Password: </label>
            <input type="password" name="password"/>
        </div>
        <div class="padd">
            <input type="radio" name="type" value="0" checked/>
            User
            <input type="radio" name="type"
                   value="1" <?= isset($_SESSION['post']['type']) && $_SESSION['post']['type'] == 1 ? 'checked' : ''; ?> />
            Admin
        </div>

        <div class="padd">
            <input type="submit" name="submit" value="Add"/>
        </div>
    </form>
    <?php $_SESSION['post'] = null;
} ?>
