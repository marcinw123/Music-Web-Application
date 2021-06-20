<?php
require 'includes/classes/Artist.php';

if (isset($_POST['sendArtistsForUpdate'])) {

    $artistName = filter_var($_POST["artistName"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $artistDescription = filter_var($_POST["artistDescription"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $artist = new Artist($dbh, $artistName, $artistDescription);
    $artist->updateArtistData();
}

if (isset($_GET['type']) && $_GET['type'] == 'delete') {
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        /** @noinspection SqlResolve */
        $selectQuery = $dbh->prepare("SELECT artist_id FROM artist WHERE user_account_id = (SELECT user_account_id FROM user_account WHERE login = ?) AND artist_id = ?");
        $selectQuery->bindParam(1, $_SESSION['userLoggedIn']);
        $selectQuery->bindValue(2, $_GET['id']);
        $selectQuery->execute();

        if ($selectQuery->rowCount() == 1) { //co oznacza ze taki artysta istnieje i zalogowany uzytkownik ma prawo do usuniecia tego artysty
            /** @noinspection SqlResolve */
            $deleteQuery = $dbh->prepare("DELETE FROM artist WHERE artist_id = ?");
            $deleteQuery->bindValue(1, $_GET['id']);
            $deleteQuery->execute();
        }
    }
}
