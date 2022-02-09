<?php
require('../helper/function.php');

$auth = new Auth($db);

if (isset($_POST['register'])) {
    $auth->register($_POST);
}


require('templates/auth_header.php');
?>
<div class="app app-auth-sign-up align-content-stretch d-flex flex-wrap justify-content-end">
    <div class="app-auth-background">

    </div>
    <div class="app-auth-container">
        <div class="logo">
            <a href="">API37 - Button API</a>
        </div>
        <?php if (isset($_SESSION['alert'])) : ?>
            <div class="alert alert-<?= $_SESSION['alert']['color'] ?> alert-style-light mt-5 mb-5" role="alert">
                <?= $_SESSION['alert']['msg'] ?>
            </div>
            <?php unset($_SESSION['alert']) ?>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="auth-credentials m-b-xxl">
            <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control m-b-md" id="name" aria-describedby="Full Name">
                <label for="username" class="form-label">Email</label>
                <input type="text" name="username" class="form-control m-b-md" id="username" aria-describedby="username">
                <label for="mobile" class="form-label">WhatsApp Number with 91</label>
                <input type="number" name="mobile" class="form-control m-b-md" id="mobile" aria-describedby="Whatsapp Number with Country Code With out +sign">
                <!--<label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" aria-describedby="password" value="///" disabled>-->
                
            </div>

            <div class="auth-submit">
                <button type="submit" name="register" class="btn btn-primary">Register</button>
                <a href="https://v3.dizitalsurface.com/pages/login.php">Login Now??</a>
<div class="danger" style="background-color: #ffdddd; border-left: 3px solid #f44336; margin-top: 20px;">
        <p><strong> Attention!!!</strong> Enter Correct WhatsApp Number You Will Get Password On WhatsApp. You Can Change It Later.</p>
      </div> 
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
require('templates/auth_footer.php');
?>