<?php

session_start();

try {
    $dbh = new PDO('mysql:host=localhost;dbname=musicappdb', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}
