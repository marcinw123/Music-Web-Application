<?php

if (isset($_SESSION['userLoggedIn'])) {
    header('Location: index.php');
}

