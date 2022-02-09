<!DOCTYPE html>
<html lang="en">
<?php
 require('../helper/function.php');

$auth = new Auth($db);

if ($auth->isLogin()) {
    header('Location: home.php');
    exit;
}
if (isset($_POST['login'])) {
    var_dump($auth->login($_POST['username'], $_POST['password']));
}


require('templates/auth_header.php');
?>


<div class="app app-auth-sign-in align-content-stretch d-flex flex-wrap justify-content-end">
    <div class="app-auth-background">

    </div>
    <div class="app-auth-container">
        <div class="logo">
            <a href="index.html">API371 - Button API2</a>
        </div>
        <?php if (isset($_SESSION['alert'])) : ?>
            <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5" role="alert">
                <?= $_SESSION['alert']['msg'] ?>
            </div>
            <?php unset($_SESSION['alert']) ?>
        <?php endif; ?>
        <p class="auth-description">Login Now<br>Don't Have Account? <a href="register.php">Register Now</a></p>
        <form action="" method="POST">
            <div class="auth-credentials m-b-xxl">
                <label for="username" class="form-label">Email</label>
                <input type="text" name="username" class="form-control m-b-md" id="username" aria-describedby="username">

                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" aria-describedby="password">
            </div>

            <div class="auth-submit">
                <button type="submit" name="login" class="btn btn-primary">Login</button>
                <a href="#" class="auth-forgot-password float-end">Forgot Password?</a>
            </div>
        </form>
        <div class="divider"></div>
        <div class="auth-alts">
            <a href="#" class="auth-alts-google"></a>
            <a href="#" class="auth-alts-facebook"></a>
            <a href="#" class="auth-alts-twitter"></a>
        </div>
    </div>
</div>
<?php
require('templates/auth_footer.php') ?>

<!-- Javascripts -->