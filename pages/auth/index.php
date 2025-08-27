<!DOCTYPE html>
<html lang="en">
<?php require_once PARTIALS_PATH . '/header.php'; ?>

<link rel="stylesheet" href="<?php getCSSFile('login.css') ?>">

<body id="login-body" style='background-image: url("<?php getImagePath('prima-bg.jpg') ?>");'>
    <div class="container login">
        <div class="login-container">
            <div class="login-box">
                <img src="<?php getImagePath('prima-logo.png') ?>" alt="Logo" class="img-responsive center-block">
                <h4 class="text-center" id="systemLabel"><?php echo SYSTEM_NAME ?></h4>

                <!-- LOGIN FORM -->
                <form id="loginForm">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12 form-element">
                                <label for="rfid">RFID</label>
                                <input type="text" id="rfid" class="form-control" placeholder="Enter your RFID">
                            </div>
                            <div class="col-lg-12 form-element">
                                <label for="password">Password</label>
                                <input type="password" id="password" class="form-control"
                                    placeholder="Enter your password">
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary btn-block" id="loginBtn">Log in</button>
                            </div>
                            <div class="col-lg-12 text-center toggle-form-con">
                                <a href="#" id="showRegister">No Account Yet? Register</a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- REGISTER FORM (hidden by default) -->
                <form id="registerForm" style="display: none;">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12 form-element">
                                <label for="rfidReg">RFID</label>
                                <input type="text" id="rfidReg" class="form-control" placeholder="Enter your RFID">
                            </div>
                            <div class="col-lg-12 form-element">
                                <label for="passwordReg">Password</label>
                                <input type="password" id="passwordReg" class="form-control"
                                    placeholder="Enter your password">
                            </div>
                            <div class="col-lg-12 form-element">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" id="confirmPassword" class="form-control"
                                    placeholder="Confirm your password">
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-success btn-block"
                                    id="registerBtn">Register</button>
                            </div>
                            <div class="col-lg-12 text-center toggle-form-con">
                                <a href="#" id="showLogin">Already have an account? Log in</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script type="text/javascript" src="<?php getJSHelper('errorFunction.js') ?>"></script>
    <script type="text/javascript" src="<?php getAjaxPath('auth/loginUser.js') ?>"></script>
    <script type="text/javascript" src="<?php getAjaxPath('auth/registerUser.js') ?>"></script>

    <script>
        // Toggle between login and register
        $("#showRegister").on("click", function (e) {
            e.preventDefault();
            $("#loginForm").hide();
            $("#registerForm").show();
        });

        $("#showLogin").on("click", function (e) {
            e.preventDefault();
            $("#registerForm").hide();
            $("#loginForm").show();
        });
    </script>
</body>

</html>