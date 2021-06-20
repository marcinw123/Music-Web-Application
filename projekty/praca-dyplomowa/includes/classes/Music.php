<?php

class Music {

    private $dbh; //db handler
    private $errorArray; // array which contains any errors pushed from validation methods
    private $messageArray;

    private $numberOfForms;
    private $playlistName;
    private $playlistType;
    private $coverFile;
    private $mainArtist;
    private $trackName = array();
    private $trackFile = array();
    private $genre = array();
    private $artists = array(array());

    public static function renderForm($dbh) {
        /** @noinspection SqlResolve */
        $query = $dbh->prepare("SELECT * FROM playlist_type");
        $query->execute();
        $playlistTypes = $query->fetchAll(PDO::FETCH_COLUMN);

        $playlistTypesOutput = "";
        foreach ($playlistTypes as $playlistType) {
            $playlistTypesOutput .= "<option value=\"$playlistType\">$playlistType</option>";
        }

        /** @noinspection SqlResolve */
        $query = $dbh->prepare("SELECT * FROM genre");
        $query->execute();
        $genres = $query->fetchAll(PDO::FETCH_COLUMN);

        $genresOutput = "";
        foreach ($genres as $genre) {
            $genresOutput .= "<option value=\"$genre\">$genre</option>";
        }

        /** @noinspection SqlResolve */
        $query = $dbh->prepare("SELECT artist_id, artist_name FROM artist ORDER BY artist_name");
        $query->execute();
        $artists = $query->fetchAll(PDO::FETCH_ASSOC);

        $artistsOutput = "";
        foreach ($artists as $artist) {
            $artistsOutput .= "<option value=\"$artist[artist_id]\">$artist[artist_name] ($artist[artist_id])</option>";
        }

        if (isset($_POST['generateSongsFormButton'])) {
            if (is_numeric($_POST['numberOfForms']) && $_POST['numberOfForms'] > 0 && $_POST['numberOfForms'] <= 19) {
                echo "<div class=\"row md-3\">
                            <div class=\"col-3\">
                                <label for=\"playlistName\" class=\"form-label\">Playlist name</label>
                                <input type=\"text\" id=\"playlistName\" name=\"playlistName\" class=\"form-control\" required>
                            </div>
                            <div class=\"col-1\">
                                <label for=\"playlistType\" class=\"form-label\">Playlist type</label>
                                <select id=\"playlistType\" name=\"playlistType\" class=\"form-select\" required>
                                    ".$playlistTypesOutput."
                                </select>
                            </div>
                            <div class=\"col-3\">
                                <label for=\"coverFile\" class=\"form-label\">Cover image</label>
                                <input class=\"form-control\" type=\"file\" id=\"coverFile\" name=\"coverFile\" accept=\"image/jpeg\" required>
                            </div>
                            <div class=\"col-3\">
                                  <label for=\"mainArtist\" class=\"form-label\">Main artist</label>
                                  <select id=\"mainArtist\" name=\"mainArtist\" class=\"form-select\" required>
                                      ".$artistsOutput."
                                  </select>
                              </div>
                      </div>
                      <br>";
                for ($i = 0; $i < $_POST['numberOfForms']; $i++) {
                    echo "<div class=\"row md-3\">
                              <div class=\"col-3\">
                                  <label for=\"trackName$i\" class=\"form-label\">Track name ". $i+1 ."</label>
                                  <input id=\"trackName$i\" name=\"trackName$i\" type=\"text\" class=\"form-control\" required>
                              </div>
                              <div class=\"col-3\">
                                  <label for=\"trackFile$i\" class=\"form-label\">Track file ". $i+1 ."</label>
                                  <input id=\"trackFile$i\" name=\"trackFile$i\" type=\"file\" class=\"form-control\" accept=\"audio/mp3\" required>
                              </div>
                              <div class=\"col-2\">
                                  <label for=\"genre$i\" class=\"form-label\">Genre</label>
                                  <select id=\"genre$i\" name=\"genre$i\" class=\"form-select\" required>
                                      ".$genresOutput."
                                  </select>
                              </div>
                              <div class=\"col-2\">
                                  <label for=\"artists$i\" class=\"form-label\">Artist/s (multiple with Ctrl)</label>
                                  <select id=\"artists$i\" name=\"artists$i".'[]'."\" class=\"form-select\" multiple size=\"3\" required>
                                      ".$artistsOutput."
                                  </select>
                              </div>
                          </div>
                          ";
                }
                echo "<div class=\"row md-3\">
                          <div class=\"col-3\">
                                <button type=\"submit\" name=\"sendMusic\" class=\"btn btn-primary\">Submit</button>
                          </div>
                      </div>";
            }
            else {
                echo '<p>Enter number between 1 and 19</p>';
            }
        }
    }
    public static function render10RandomPlaylists($dbh) {
        /** @noinspection SqlResolve */
        $playlistQuery = $dbh->prepare("SELECT playlist_id, playlist_name, cover_file, artist_name FROM playlist JOIN artist USING(artist_id) ORDER BY RAND() LIMIT 10");
        $playlistQuery->execute();

        $playlistResult = $playlistQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($playlistResult as $row) {
            echo "<div onclick=\"location.href='playlist.php?id=$row[playlist_id]'\" class=\"col-md-2 p-3 m-1\" style=\"background-color: #3e3d3d; cursor: pointer\">
                      <img src='data:image/jpeg;base64,".base64_encode($row['cover_file'])."' class='w-100' alt=''>
                      <p class='fs-5 fw-bold'>".$row['playlist_name']."</p>
                      <p class='fst-light'>".$row['artist_name']."</p>
                  </div>";
        }
    }
    public static function renderUserPlaylists($dbh) {
        /** @noinspection SqlResolve */
        $playlistQuery = $dbh->prepare("SELECT playlist_id, cover_file, playlist_name, artist_name FROM playlist JOIN artist USING(artist_id) WHERE playlist.user_account_id = (SELECT user_account_id FROM user_account WHERE login = ?)");
        $playlistQuery->bindValue(1, $_SESSION['userLoggedIn']);
        $playlistQuery->execute();

        $playlistResult = $playlistQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($playlistResult as $row) {
            echo "<div class=\"col-md-2 p-3 m-1\">
                      <div onclick=\"location.href='playlist.php?id=$row[playlist_id]'\" class='row' style=\"background-color: #3e3d3d; cursor: pointer\">
                            <img src='data:image/jpeg;base64,".base64_encode($row['cover_file'])."' class='w-100 p-3' alt=''>
                            <p class='fs-5 fw-bold'>".$row['playlist_name']."</p>
                            <p class='fst-light'>".$row['artist_name']."</p>
                      </div>
                      <div class='row'>
                            <div class='col-6'>
                                <button onclick=\"location.href='manageSongs.php?id=$row[playlist_id]&type=edit'\" class='btn btn-warning w-100'>Edit</button>
                            </div>
                            <div class='col-6'>
                                <button onclick=\"location.href='manageSongs.php?id=$row[playlist_id]&type=delete'\" class='btn btn-danger w-100'>Delete</button>
                            </div>
                      </div>
                  </div>";
        }
    }
    public static function renderFormForUpdate($dbh) {
        if (isset($_GET['type']) && $_GET['type'] == 'edit') {
            if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
                /** @noinspection SqlResolve */
                $playlistQuery = $dbh->prepare("SELECT * FROM playlist JOIN artist USING(artist_id) WHERE playlist_id = ? AND playlist.user_account_id = (SELECT user_account_id FROM user_account WHERE login = ?)");
                $playlistQuery->bindValue(1, $_GET['id']);
                $playlistQuery->bindValue(2, $_SESSION['userLoggedIn']);
                $playlistQuery->execute();

                if ($playlistQuery->rowCount() == 1) {
                    $playlistResult = $playlistQuery->fetchAll(PDO::FETCH_ASSOC);

                    /** @noinspection SqlResolve */
                    $trackQuery = $dbh->prepare("SELECT * FROM track WHERE playlist_id = ? ORDER BY order_in_playlist");
                    $trackQuery->bindValue(1, $_GET['id']);
                    $trackQuery->execute();
                    $trackResult = $trackQuery->fetchAll(PDO::FETCH_ASSOC);

                    /** @noinspection SqlResolve */
                    $artistHasTrackQuery = $dbh->prepare("SELECT artist_id, artist_name, track_id FROM artist_has_track JOIN track USING(track_id) JOIN artist USING(artist_id) WHERE playlist_id = ?");
                    $artistHasTrackQuery->bindValue(1, $_GET['id']);
                    $artistHasTrackQuery->execute();
                    $artistHasTrackResult = $artistHasTrackQuery->fetchAll(PDO::FETCH_ASSOC);


                    /** @noinspection SqlResolve */
                    $query = $dbh->prepare("SELECT * FROM playlist_type");
                    $query->execute();
                    $playlistTypes = $query->fetchAll(PDO::FETCH_COLUMN);

                    $playlistTypesOutput = "";
                    foreach ($playlistTypes as $playlistType) {
                        if ($playlistType == $playlistResult[0]['playlist_type_id']) {
                            $playlistTypesOutput .= "<option value=\"$playlistType\" selected>$playlistType</option>";
                        }
                        else {
                            $playlistTypesOutput .= "<option value=\"$playlistType\">$playlistType</option>";
                        }
                    }

                    /** @noinspection SqlResolve */
                    $query = $dbh->prepare("SELECT * FROM genre");
                    $query->execute();
                    $genres = $query->fetchAll(PDO::FETCH_COLUMN);

                    $genresOutput = array_fill(0, sizeof($trackResult), '');
                    for ($i = 0; $i < sizeof($genresOutput); $i++) {
                        foreach ($genres as $genre) {
                            if ($trackResult[$i]['genre_id'] == $genre) {
                                $genresOutput[$i] .= "<option value=\"$genre\" selected>$genre</option>";
                            }
                            else {
                                $genresOutput[$i] .= "<option value=\"$genre\">$genre</option>";
                            }
                        }
                    }


                    /** @noinspection SqlResolve */
                    $query = $dbh->prepare("SELECT artist_id, artist_name FROM artist ORDER BY artist_name");
                    $query->execute();
                    $artists = $query->fetchAll(PDO::FETCH_ASSOC);

                    $mainArtistOutput = "";
                    foreach ($artists as $artist) {
                        if ($artist['artist_id'] == $playlistResult[0]['artist_id']) {
                            $mainArtistOutput .= "<option value=\"$artist[artist_id]\" selected>$artist[artist_name] ($artist[artist_id])</option>";
                        }
                        else {
                            $mainArtistOutput .= "<option value=\"$artist[artist_id]\">$artist[artist_name] ($artist[artist_id])</option>";
                        }
                    }

                    $trackArtistsOutput = array_fill(0, sizeof($trackResult), '');
                    for ($i = 0; $i < sizeof($trackArtistsOutput); $i++) {
                        foreach ($artists as $artist) {
                            $temp = array();
                            foreach ($artistHasTrackResult as $row) {
                                if ($trackResult[$i]['track_id'] == $row['track_id'] && $artist['artist_id'] == $row['artist_id']) {
                                    $trackArtistsOutput[$i] .= "<option value=".$artist['artist_id']." selected>".$artist['artist_name']." (".$artist['artist_id'].")</option>";
                                    array_push($temp, $artist['artist_id']);
                                }
                            }
                            if (!in_array($artist['artist_id'], $temp)) {
                                $trackArtistsOutput[$i] .= "<option value=".$artist['artist_id'].">".$artist['artist_name']." (".$artist['artist_id'].")</option>";
                            }
                        }
                    }

                    echo "<form action='manageSongs.php' method='post' enctype='multipart/form-data'>
                              <div class=\"row md-3\">
                                    <div class=\"col-3\">
                                        <label for=\"playlistName\" class=\"form-label\">Playlist name</label>
                                        <input type=\"text\" id=\"playlistName\" name=\"playlistName\" class=\"form-control\" value='".$playlistResult[0]['playlist_name']."'>
                                        <input type='hidden' name='playlistId' value='".$playlistResult[0]['playlist_id']."'>
                                        <input type='hidden' name='amountOfTracks' value='".sizeof($trackResult)."'>
                                    </div>
                                    <div class=\"col-1\">
                                        <label for=\"playlistType\" class=\"form-label\">Playlist type</label>
                                        <select id=\"playlistType\" name=\"playlistType\" class=\"form-select\">
                                            ".$playlistTypesOutput."
                                        </select>
                                    </div>
                                    <div class=\"col-3\">
                                        <label for=\"coverFile\" class=\"form-label\">Cover image (leave empty when not updating)</label>
                                        <input class=\"form-control\" type=\"file\" id=\"coverFile\" name=\"coverFile\" accept=\"image/jpeg\">
                                    </div>
                                    <div class=\"col-3\">
                                          <label for=\"mainArtist\" class=\"form-label\">Main artist</label>
                                          <select id=\"mainArtist\" name=\"mainArtist\" class=\"form-select\">
                                              ".$mainArtistOutput."
                                          </select>
                                    </div>
                              </div>
                              <br>";

                    for ($i = 0; $i < sizeof($trackResult); $i++) {
                        echo "<div class=\"row md-3\">
                                  <div class=\"col-3\">
                                      <label for=\"trackName$i\" class=\"form-label\">Track name ". $i+1 ."</label>
                                      <input id=\"trackName$i\" name=\"trackName$i\" type=\"text\" class=\"form-control\" value='".$trackResult[$i]['track_name']."'>
                                      <input type='hidden' name='trackId$i' value='".$trackResult[$i]['track_id']."'>
                                  </div>
                                  <div class=\"col-3\">
                                      <label for=\"trackFile$i\" class=\"form-label\">Track file ". $i+1 ." (leave empty when not updating)</label>
                                      <input id=\"trackFile$i\" name=\"trackFile$i\" type=\"file\" class=\"form-control\" accept=\"audio/mp3\">
                                  </div>
                                  <div class=\"col-2\">
                                      <label for=\"genre$i\" class=\"form-label\">Genre</label>
                                      <select id=\"genre$i\" name=\"genre$i\" class=\"form-select\">
                                          ".$genresOutput[$i]."
                                      </select>
                                  </div>
                                  <div class=\"col-2\">
                                      <label for=\"artists$i\" class=\"form-label\">Artist/s (multiple with Ctrl)</label>
                                      <select id=\"artists$i\" name=\"artists$i".'[]'."\" class=\"form-select\" multiple size=\"3\">
                                          ".$trackArtistsOutput[$i]."
                                      </select>
                                  </div>
                              </div>";
                    }
                }
                echo "<div class=\"row md-3\">
                          <div class=\"col-3\">
                                <button type=\"submit\" name=\"sendMusicForUpdate\" class=\"btn btn-primary\">Submit</button>
                          </div>
                      </div>
                      </form>";
            }
        }
    }
    public static function renderPlaylistPage($dbh) {
        if (isset($_GET['id'])) {
            $playlistId = $_GET['id'];
            if (isset($playlistId) && is_numeric($playlistId) && $playlistId > 0) {
                /** @noinspection SqlResolve */
                $playlistQuery = $dbh->prepare("SELECT playlist_name, cover_file, playlist_type_id, artist_name FROM playlist JOIN artist USING(artist_id) WHERE playlist_id = ?");
                $playlistQuery->bindValue(1, $playlistId);
                $playlistQuery->execute();
                $playlistResult = $playlistQuery->fetchAll(PDO::FETCH_ASSOC);

                /** @noinspection SqlResolve */
                $trackQuery = $dbh->prepare("SELECT track_id, track_name, track_file, order_in_playlist, genre_id FROM track WHERE playlist_id = ? ORDER BY order_in_playlist");
                $trackQuery->bindValue(1, $playlistId);
                $trackQuery->execute();
                $trackResult = $trackQuery->fetchAll(PDO::FETCH_ASSOC);

                /** @noinspection SqlResolve */
                $artist_has_trackQuery = $dbh->prepare("SELECT track_id, artist_name FROM artist_has_track JOIN track USING(track_id) JOIN artist USING(artist_id) WHERE playlist_id = ?");
                $artist_has_trackQuery->bindValue(1, $playlistId);
                $artist_has_trackQuery->execute();
                $artistHasTrackResult = $artist_has_trackQuery->fetchAll(PDO::FETCH_ASSOC);

                echo "<div class=\"row p-2 m-1\" style='background-color: #474747;'>
                            <div class=\"col-md-2\">
                                <img src='data:image/jpeg;base64,".base64_encode($playlistResult[0]['cover_file'])."' class='w-100' alt=''>
                            </div>
                            <div class=\"col-10 container-fluid\">
                                <div class=\"row p-2 fs-4 fst-italic\">
                                    ".$playlistResult[0]['playlist_type_id']."
                                </div>
                                <div class=\"row p-2 fs-2 fw-bold\">
                                    ".$playlistResult[0]['playlist_name']."
                                </div>
                                <div class=\"row p-2 fs-3 fw-lighter\">
                                    ".$playlistResult[0]['artist_name']."
                                </div> 
                            </div>
                      </div>";

                foreach ($trackResult as $trackRow) {

                    $artists = array();

                    foreach ($artistHasTrackResult as $artistRow) {
                        if ($trackRow['track_id'] == $artistRow['track_id']) {
                            array_push($artists, $artistRow['artist_name']);
                        }
                    }

                    echo "<div class='row p-2 m-1' style='background-color: #3e3d3d;'>
                            <div class='col-1 fst-italic'>
                                $trackRow[order_in_playlist]
                            </div>
                            <div class='col-3 container-fluid'>
                                <div class='row fs-6 fw-bold'>
                                    $trackRow[track_name]
                                </div>
                                <div class='row'>
                                    ".implode(", ", $artists)."
                                </div>
                            </div>
                            <div class='col-7'>
                                <audio controls>
                                    <source src='data:audio/mpeg;base64,".base64_encode($trackRow['track_file'])."' type='audio/mpeg'>
                                </audio>
                            </div>
                            <div class='col-1'>
                                $trackRow[genre_id]
                            </div>
                      </div>
                      ";
                }
            }
        }
        else {
            echo '<p>You shouldn\'t be here</p>';
        }
    }
    public static function showSearchResults($dbh) {
        if (isset($_GET['searchButton']) && isset($_GET['search'])) {
            $searchData = filter_var($_GET['search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            /** @noinspection SqlResolve */
            $queryForPlaylists = $dbh->prepare("SELECT playlist_id, playlist_name, cover_file, artist_name FROM playlist JOIN artist USING(artist_id) WHERE LOWER(playlist_name) LIKE ? OR LOWER(artist_name) LIKE ?");
            $queryForPlaylists->bindValue(1, "%$searchData%");
            $queryForPlaylists->bindValue(2, "%$searchData%");
            $queryForPlaylists->execute();
            $playlistResult = $queryForPlaylists->fetchAll(PDO::FETCH_ASSOC);

            /** @noinspection SqlResolve */
            $queryForTracks = $dbh->prepare("SELECT track_id, track_name, playlist_id, playlist_name FROM track JOIN artist_has_track USING(track_id) JOIN artist USING(artist_id) JOIN playlist USING(playlist_id) WHERE LOWER(track_name) LIKE ? OR artist_name LIKE ?");
            $queryForTracks->bindValue(1, "%$searchData%");
            $queryForTracks->bindValue(2, "%$searchData%");
            $queryForTracks->execute();
            $tracksResult = $queryForTracks->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2>Playlists</h2>";
            if ($queryForPlaylists->rowCount() > 0) {
                foreach ($playlistResult as $row) {
                    echo "<div onclick=\"location.href='playlist.php?id=$row[playlist_id]'\" class=\"col-md-2 p-3 m-1\" style=\"background-color: #3e3d3d; cursor: pointer\">
                              <img src='data:image/jpeg;base64,".base64_encode($row['cover_file'])."' class='w-100' alt=''>
                              <p class='fw-bold'>".$row['playlist_name']."</p>
                              <p class='fst-light'>".$row['artist_name']."</p>
                          </div>";
                }
            }
            else {
                echo "<div class='col-6 fst-italic'>No results</div>";
            }

            echo "<h2>Tracks</h2>";
            if ($queryForTracks->rowCount() > 0) {
                foreach ($tracksResult as $row) {
                    echo "<div onclick=\"location.href='playlist.php?id=$row[playlist_id]'\" class=\"col-md-12 p-2 m-1\" style=\"background-color: #3e3d3d; cursor: pointer\">
                              <p class='fw-bold'>$row[track_name]</p>
                              <p class='fst-italic'>in playlist: $row[playlist_name]</p>
                          </div>";
                }
            }
            else {
                echo "<div class='col-6 fst-italic'>No results</div>";
            }
        }

    }

    public function __construct($dbh, $numberOfForms, $playlistName, $playlistType, $coverFile, $mainArtist, $trackName, $trackFile, $genre, $artists) {
        $this->dbh = $dbh;
        $this->errorArray = array();
        $this->messageArray = array();

        $this->numberOfForms = $numberOfForms;
        $this->playlistName = $playlistName;
        $this->playlistType = $playlistType;
        $this->coverFile = $coverFile;
        $this->mainArtist = $mainArtist;

        $this->trackName = $trackName;
        $this->trackFile = $trackFile;
        $this->genre = $genre;
        $this->artists = $artists;
    }

    private function validateImageDimensions() {
        list($width, $height) = (getimagesize($this->coverFile['tmp_name']));
        if ($width != $height) {
            array_push($this->errorArray, "Width and height of uploaded image needs to have same value (image must be a square)");
            return;
        }
        if ($width < 150 || $height < 150 || $width > 1500 || $height > 1500) {
            array_push($this->errorArray, "Height and width of image needs to be between 150px and 1500px");
        }
    }


    private function queriesForAddingMusicData(): bool {
        $this->dbh->beginTransaction();

        /** @noinspection SqlResolve */
        $queryForPlaylistTable = $this->dbh->prepare("INSERT INTO Playlist (playlist_name, cover_file, playlist_type_id, artist_id, user_account_id) VALUES (?, ?, ?, ?, (SELECT user_account_id FROM user_account WHERE login = ?))");

        $actualFileStreamForImage = file_get_contents($this->coverFile['tmp_name']);

        $queryForPlaylistTable->bindParam(1, $this->playlistName);
        $queryForPlaylistTable->bindParam(2, $actualFileStreamForImage);
        $queryForPlaylistTable->bindParam(3, $this->playlistType);
        $queryForPlaylistTable->bindParam(4, $this->mainArtist);
        $queryForPlaylistTable->bindParam(5, $_SESSION['userLoggedIn']);

        $queryForPlaylistTable->execute();

        $lastInsertedPlaylistId = $this->dbh->lastInsertId();

        /** @noinspection SqlResolve */
        $queryForTrackTable = $this->dbh->prepare("INSERT INTO Track (track_name, track_file, order_in_playlist, playlist_id, genre_id) VALUES (?, ?, ?, ?, ?)");

        $lastInsertedTrackId = array();
        for ($i = 0; $i < $this->numberOfForms; $i++) {
            $actualFileStreamForAudio = file_get_contents($this->trackFile[$i]['tmp_name']);

            $queryForTrackTable->bindParam(1, $this->trackName[$i]);
            $queryForTrackTable->bindParam(2, $actualFileStreamForAudio);
            $queryForTrackTable->bindValue(3, $i + 1);
            $queryForTrackTable->bindParam(4, $lastInsertedPlaylistId);
            $queryForTrackTable->bindParam(5, $this->genre[$i]);

            $queryForTrackTable->execute();

            $lastInsertedTrackId[$i] = $this->dbh->lastInsertId();
        }

        /** @noinspection SqlResolve */
        $queryForArtistHasTrackTable = $this->dbh->prepare("INSERT INTO artist_has_track (artist_id, track_id) VALUES (?, ?)");
        for ($i = 0; $i < $this->numberOfForms; $i++) {
            for ($j = 0; $j < sizeof($this->artists[$i]); $j++) {
                $queryForArtistHasTrackTable->bindParam(1, $this->artists[$i][$j]);
                $queryForArtistHasTrackTable->bindParam(2, $lastInsertedTrackId[$i]);

                $queryForArtistHasTrackTable->execute();
            }
        }
        return $this->dbh->commit();
    }
    private function queriesForUpdatingMusicData(): bool {
        $this->dbh->beginTransaction();

        /** @noinspection SqlResolve */
        $queryForPlaylistTableWithCoverFile = $this->dbh->prepare("UPDATE playlist SET playlist_name = ?, cover_file = ?, playlist_type_id = ?, artist_id = ? WHERE playlist_id = ?");
        /** @noinspection SqlResolve */
        $queryForPlaylistTableWithoutCoverFile = $this->dbh->prepare("UPDATE playlist SET playlist_name = ?, playlist_type_id = ?, artist_id = ? WHERE playlist_id = ?");

        if ($this->coverFile != NULL) {
            $actualFileStream = file_get_contents($this->coverFile['tmp_name']);

            $queryForPlaylistTableWithCoverFile->bindParam(1, $this->playlistName);
            $queryForPlaylistTableWithCoverFile->bindParam(2, $actualFileStream);
            $queryForPlaylistTableWithCoverFile->bindParam(3, $this->playlistType);
            $queryForPlaylistTableWithCoverFile->bindParam(4, $this->mainArtist);
            $queryForPlaylistTableWithCoverFile->bindValue(5, $_POST['playlistId']);

            $queryForPlaylistTableWithCoverFile->execute();
        }
        else {
            $queryForPlaylistTableWithoutCoverFile->bindParam(1, $this->playlistName);
            $queryForPlaylistTableWithoutCoverFile->bindParam(2, $this->playlistType);
            $queryForPlaylistTableWithoutCoverFile->bindParam(3, $this->mainArtist);
            $queryForPlaylistTableWithoutCoverFile->bindValue(4, $_POST['playlistId']);

            $queryForPlaylistTableWithoutCoverFile->execute();
        }

        /** @noinspection SqlResolve */
        $queryForTrackTableWithTrackFile = $this->dbh->prepare("UPDATE track SET track_name = ?, track_file = ?, genre_id = ? WHERE track_id = ? AND playlist_id = ?");
        /** @noinspection SqlResolve */
        $queryForTrackTableWithoutTrackFile = $this->dbh->prepare("UPDATE track SET track_name = ?, genre_id = ? WHERE track_id = ? AND playlist_id = ?");

        for ($i = 0; $i < $this->numberOfForms; $i++) {
            if ($this->trackFile[$i] != NULL) {
                $actualFileStream = file_get_contents($this->trackFile[$i]['tmp_name']);

                $queryForTrackTableWithTrackFile->bindParam(1, $this->trackName[$i]);
                $queryForTrackTableWithTrackFile->bindParam(2, $actualFileStream);
                $queryForTrackTableWithTrackFile->bindParam(3, $this->genre[$i]);
                $queryForTrackTableWithTrackFile->bindValue(4, $_POST["trackId$i"]);
                $queryForTrackTableWithTrackFile->bindValue(5, $_POST['playlistId']);

                $queryForTrackTableWithTrackFile->execute();
            }
            else {
                $queryForTrackTableWithoutTrackFile->bindParam(1, $this->trackName[$i]);
                $queryForTrackTableWithoutTrackFile->bindParam(2, $this->genre[$i]);
                $queryForTrackTableWithoutTrackFile->bindValue(3, $_POST["trackId$i"]);
                $queryForTrackTableWithoutTrackFile->bindValue(4, $_POST['playlistId']);

                $queryForTrackTableWithoutTrackFile->execute();
            }
        }

        /** @noinspection SqlResolve */
        $deleteQueryForArtistHasTrackTable = $this->dbh->prepare("DELETE FROM artist_has_track WHERE track_id = ?");
        /** @noinspection SqlResolve */
        $insertQueryForArtistHasTrackTable = $this->dbh->prepare("INSERT INTO artist_has_track (artist_id, track_id) VALUES (?, ?)");

        for ($i = 0; $i < $this->numberOfForms; $i++) {
            $deleteQueryForArtistHasTrackTable->bindParam(1, $_POST["trackId$i"]);
            $deleteQueryForArtistHasTrackTable->execute();

            for ($j = 0; $j < sizeof($this->artists[$i]); $j++) {
                $insertQueryForArtistHasTrackTable->bindParam(1, $this->artists[$i][$j]);
                $insertQueryForArtistHasTrackTable->bindParam(2, $_POST["trackId$i"]);

                $insertQueryForArtistHasTrackTable->execute();
            }
        }

        return $this->dbh->commit();
    }

    public function addMusicData(): bool {
        $this->validateImageDimensions();

        if (empty($this->errorArray)) {
            array_push($this->messageArray, "Data inserted");
            return $this->queriesForAddingMusicData();
        }
        else {
            return false;
        }
    }
    public function updateMusicData(): bool {
        if ($this->coverFile != null) {
            $this->validateImageDimensions();
        }

        if (empty($this->errorArray)) {
            array_push($this->messageArray, "Data updated");
            return $this->queriesForUpdatingMusicData();
        }
        else {
            return false;
        }
    }

    public function returnAllMessages() {
        if (!empty($this->errorArray)) {
            foreach ($this->errorArray as $item) {
                echo "<p>$item</p>";
            }
        }
        if (!empty($this->messageArray)) {
            foreach ($this->messageArray as $item) {
                echo "<p>$item</p>";
            }
        }
    }

}