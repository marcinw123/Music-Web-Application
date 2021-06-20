<?php

require 'includes/config.php';
require 'includes/utils/indexHeader.php';
require 'includes/handlers/registerHandler.php';
require 'includes/handlers/loginHandler.php';
require 'includes/utils/rememberFunctions.php';

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Music App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <form id="loginForm" action="register.php" method="POST" accept-charset="UTF-8">
                    <h2>Login to your account</h2>

                    <label for="loginUsername" class="form-label">Username</label>
                    <input id="loginUsername" name="loginUsername" type="text" class="form-control" required>

                    <label for="loginPassword" class="form-label">Password</label>
                    <input id="loginPassword" name="loginPassword" type="password" class="form-control" required>

                    <button type="submit" name="loginButton" class="btn btn-primary">LOG IN</button>
                </form>
            </div>

            <div class="col-md-6">
                <form id="registerForm" action="register.php" method="POST" accept-charset="UTF-8">

                    <h2>Register your account</h2>

                    <label for="registerName" class="form-label">Name</label>
                    <input id="registerName" name="registerName" type="text" class="form-control" value="<?php rememberRegisterName(); ?>" required>

                    <label for="registerSurname" class="form-label">Surname</label>
                    <input id="registerSurname" name="registerSurname" type="text" class="form-control" value="<?php rememberRegisterSurname(); ?>" required>

                    <label for="registerUsername" class="form-label">Username</label>
                    <input id="registerUsername" name="registerUsername" type="text" class="form-control" value="<?php rememberRegisterUsername(); ?>" required>

                    <label for="registerPassword" class="form-label">Password</label>
                    <input id="registerPassword" name="registerPassword" type="password" class="form-control" required>

                    <label for="registerPasswordConfirmation" class="form-label">Confirm password</label>
                    <input id="registerPasswordConfirmation" name="registerPasswordConfirmation" type="password" class="form-control" required>

                    <button type="submit" name="registerButton" class="btn btn-secondary">SIGN UP</button>
                </form>
            </div>
        </div>
        <div class="row">
            <?php if (isset($account)) $account->returnAllMessages(); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>