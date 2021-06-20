<?php
require 'includes/classes/Music.php';


if (isset($_POST['sendMusicForUpdate'])) {
    $numberOfForms = filter_var($_POST['amountOfTracks'], FILTER_SANITIZE_NUMBER_INT);
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

    $music->updateMusicData();
}

if (isset($_GET['type']) && $_GET['type'] == 'delete') {
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        /** @noinspection SqlResolve */
        $selectQuery = $dbh->prepare("SELECT playlist_id FROM playlist WHERE user_account_id = (SELECT user_account_id FROM user_account WHERE login = ?) AND playlist_id = ?");
        $selectQuery->bindParam(1, $_SESSION['userLoggedIn']);
        $selectQuery->bindValue(2, $_GET['id']);
        $selectQuery->execute();

        if ($selectQuery->rowCount() == 1) {
            /** @noinspection SqlResolve */
            $deleteQuery = $dbh->prepare("DELETE FROM playlist WHERE playlist_id = ?");
            $deleteQuery->bindValue(1, $_GET['id']);
            $deleteQuery->execute();
        }
    }
}
