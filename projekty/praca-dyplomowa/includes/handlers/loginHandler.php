<?php

if (isset($_POST['loginButton'])) {
    $loginUsername = filter_var($_POST['loginUsername'], FILTER_SANITIZE_SPECIAL_CHARS);
    $loginPassword = filter_var($_POST['loginPassword'], FILTER_SANITIZE_SPECIAL_CHARS);

    /** @var PDO $dbh */
    $account = new Account($dbh, NULL, NULL, $loginUsername, $loginPassword, NULL);

    $wasSuccessful = $account->login();
    if ($wasSuccessful) {
        $_SESSION['userLoggedIn'] = $loginUsername;
        header('Location: index.php');
    }
}
