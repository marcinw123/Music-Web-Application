<?php

require 'includes\classes\Account.php';
require 'includes\utils\sanitizeFunctions.php';

if (isset($_POST['registerButton'])) {
    $registerName = sanitizeFormString($_POST['registerName']);
    $registerSurname = sanitizeFormString($_POST['registerSurname']);
    $registerUsername = sanitizeFormUsername($_POST['registerUsername']);
    $registerPassword = sanitizeFormPassword($_POST['registerPassword']);
    $registerPasswordConfirmation = sanitizeFormPassword($_POST['registerPasswordConfirmation']);

    /** @var PDO $dbh */
    $account = new Account($dbh, $registerName, $registerSurname, $registerUsername, $registerPassword, $registerPasswordConfirmation);

    $wasSuccessful = $account->register();
    if ($wasSuccessful) {
        $_SESSION['userLoggedIn'] = $registerUsername;
        header('Location: index.php');
    }
}



