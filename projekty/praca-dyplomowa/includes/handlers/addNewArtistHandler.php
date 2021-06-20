<?php

require 'includes\config.php';
require 'includes\classes\Artist.php';

if (isset($_POST['sendArtists'])) {
    $numberOfForms = filter_var($_POST['numberOfForms'], FILTER_SANITIZE_NUMBER_INT);
    $artistName = array();
    $artistDescription = array();

    for ($i = 0; $i < $numberOfForms; $i++) {
        $artistName[$i] = filter_var($_POST["artistName$i"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $artistDescription[$i] = filter_var($_POST["artistDescription$i"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /** @var PDO $dbh */
    $artist = new Artist($dbh, $artistName, $artistDescription);

    $artist->insertArtistData();
}


