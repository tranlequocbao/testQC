<?php

    if(!isset($_SESSION)) session_start();
    if(isset($_SESSION['logined']) && !isset($_SESSION['change_password'])){
        header('Location: ../');
    }
    
    function unsetSession(array $arrs){
        if(!isset($_SESSION)) session_start();
        foreach ($arrs as $arr){
            if(isset($_SESSION[$arr])){
                unset($_SESSION[$arr]);
            }
        }
        return true;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	
	<div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-form-title" style="background-image: url(images/backgroundbanner.jpeg);">
					<span class="login100-form-title-1">
                        <?= isset($_SESSION['change_password']) ? 'Change Password' : 'Login'?>
                    </span>
                </div>

                <?php if(isset($_SESSION['notif'])) : ?>
                    <div class="form-group m-0 text-center">
                        <span class="alert alert-success d-inline-block w-75 mt-3 mb-0"><?=$_SESSION['notif']?></span>
                    </div>
                <?php endif; ?>

                <div class="form-group m-0 mt-2 text-center">
                    <span class="error-input"><?=$_SESSION['error'] ?? ''?></span>
                </div>

                <form class="login100-form validate-form form_login form_login_nomal" action="<?= isset($_SESSION['change_password'])  ? './change_password.php' : './login.php' ?>" method="post" style="display: none">
                    <?php if(!isset($_SESSION['change_password'])) : ?>
                        <div class="wrap-input100 validate-input m-b-26" data-validate="Username is required">
                            <span class="label-input100">Username</span>
                            <input class="input100" type="text" id="username" name="username" placeholder="Enter username" value="<?=$_SESSION['old']['username'] ?? ''?>">
                            <span class="focus-input100"></span>
                        </div>
                        <div class="wrap-input100 validate-input m-b-18" data-validate = "Password is required">
                            <span class="label-input100">Password</span>
                            <input class="input100" type="password" id="password" name="password" placeholder="Enter password">
                            <span class="focus-input100"></span>
                        </div>
                    <?php else : ?>
                        <div class="wrap-input100 validate-input m-b-26" data-validate="Old Password is required">
                            <span class="label-input100">Old Password</span>
                            <input class="input100" type="password" id="old_username" name="old_username" placeholder="Old Password">
                            <span class="focus-input100"></span>
                        </div>
                        <div class="wrap-input100 validate-input m-b-18" data-validate = "New password is required">
                            <span class="label-input100">New Password</span>
                            <input class="input100" type="password" id="new_password" name="new_password" placeholder="New password">
                            <span class="focus-input100"></span>
                        </div>
                        <div class="wrap-input100 validate-input m-b-18" data-validate = "Re New password is required">
                            <span class="label-input100">Re_New Password</span>
                            <input class="input100" type="password" id="re_new_password" name="re_new_password" placeholder="Re New password">
                            <span class="focus-input100"></span>
                        </div>
                    <?php endif; ?>

                    <div class="container-login100-form-btn">
                        <button class="btn btn-success ml-3 mr-3" type="submit" name="submit">Login</button>
                        <button class="btn btn-primary ml-3 mr-3 loginUserCode">Login with user code</button>
                    </div>
                </form>
                <form class="login100-form form_login form_login_user_code" action="./login.php" method="post">
                    <input type="hidden" name="typeLogin" value="true">
                    <div class="wrap-input100 m-b-26" data-validate="Username is required">
                        <span class="label-input100">User Code</span>
                        <input class="input100" type="password" id="usercode" name="usercode" placeholder="Waiting qr check ...">
                    </div>
                    <div class="container-login100-form-btn">
                        <button class="btn btn-success ml-3 mr-3" type="submit" name="submit">Login</button>
                        <button class="btn btn-primary ml-3 mr-3 loginNomal">Login with Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="js/main.js"></script>

    <?php if(isset($_SESSION['change_password'])) : ?>
        <script>
            $(document).ready(function () {
                $(".form_login").on('submit',function () {
                    if($("#new_password").val().trim() != $("#re_new_password").val().trim()){
                        $(".error-input").text("Password not match!");
                        return false;
                    }
                })
            })
        </script>
    <?php endif; ?>

    <script>
        $(document).ready(function () {
            $(".loginUserCode").click(function () {
                $(".form_login_nomal").hide(200);
                $(".form_login_user_code").show(200);
                return false;
            })

            $(".loginNomal").click(function () {
                $(".form_login_nomal").show(200);
                $(".form_login_user_code").hide(200);
                return false;
            })
        })
    </script>

</body>
	<?php
        unsetSession(['old','error', 'notif']);
	?>
</html>