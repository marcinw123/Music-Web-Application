<?php

require 'includes/config.php';
require 'includes/classes/Music.php';

if (isset($_POST['sendMusic'])) {

    $numberOfForms = filter_var($_POST['numberOfForms'], FILTER_SANITIZE_NUMBER_INT);
    $playlistName = filter_var($_POST['playlistName'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $playlistType = filter_var($_POST['playlistType'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($_FILES['coverFile']['error'] == 4) {
        $coverFile = NULL;
    }
    else {
        $coverFile = $_FILES['coverFile'];
    }
    $mainArtist = filter_var($_POST['mainArtist'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $trackName = array();
    $trackFile = array();
    $genre = array();
    $artists = array();
    for ($i = 0; $i < $numberOfForms; $i++) {
        $trackName[$i] = filter_var($_POST["trackName$i"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($_FILES["trackFile$i"]['error'] == 4) {
            $trackFile[$i] = NULL;
        }
        else {
            $trackFile[$i] = $_FILES["trackFile$i"];
        }
        $genre[$i] = filter_var($_POST["genre$i"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $artists[$i] = $_POST["artists$i"];
    }

    /** @var PDO $dbh */
    $music = new Music($dbh, $numberOfForms, $playlistName, $playlistType, $coverFile, $mainArtist, $trackName, $trackFile, $genre, $artists);

    $music->addMusicData();

}


