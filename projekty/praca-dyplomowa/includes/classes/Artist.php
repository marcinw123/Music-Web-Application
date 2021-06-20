<?php
require 'includes/utils/sanitizeFunctions.php';

class Artist {

    private $dbh; //db handler
    private $errorArray; // array which contains any errors pushed from validation methods
    private $messageArray;

    private $artistName = array();
    private $artistDescription = array();

    public static function renderForm() {
        if (isset($_POST['generateArtistFormButton'])) {
            if (is_numeric($_POST['numberOfForms']) && $_POST['numberOfForms'] > 0 && $_POST['numberOfForms'] <= 20) {
                for ($i = 0; $i < $_POST['numberOfForms']; $i++) {
                    echo "<div class=\"row md-3\">
                              <div class=\"col-3\">
                                  <label for=\"artistName$i\" class=\"form-label\">Artist ". $i+1 ."</label>
                                  <input id=\"artistName$i\" name=\"artistName$i\" type=\"text\" class=\"form-control\" required>
                              </div>
                              <div class=\"col-6\">
                                  <label for=\"artistDescription$i\" class=\"form-label\">Description for artist ". $i+1 ."</label>
                                  <input id=\"artistDescription$i\" name=\"artistDescription$i\" type=\"text\" class=\"form-control\">
                              </div>
                          </div>";
                }
                echo "<button type=\"submit\" name=\"sendArtists\" class=\"btn btn-primary\">Submit</button>";
            }
            else {
                echo '<p>Enter number between 1 and 20</p>';
            }
        }
    }
    public static function showTableOfArtists(PDO $dbh) {
        /** @noinspection SqlResolve */
        $query = $dbh->prepare("SELECT * FROM artist WHERE user_account_id = (SELECT user_account_id FROM user_account WHERE login = ?)");
        $query->bindParam(1, $_SESSION['userLoggedIn']);
        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        echo "<table class='table table-dark table-hover'>
                <thead>
                    <tr>
                      <th scope='col'>#</th>
                      <th scope='col'>Artist name</th>
                      <th scope='col'></th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($result as $row) {
            echo "<tr>
                      <th scope='row'>$row[artist_id]</th>
                      <td>$row[artist_name]</td>
                      <td>
                        <button onclick=\"location.href='manageArtists.php?id=$row[artist_id]&type=edit'\" type='button' name='editArtist' class='btn btn-warning'>Edit</button>
                        <button onclick=\"location.href='manageArtists.php?id=$row[artist_id]&type=delete'\" type='button' name='deleteArtist' class='btn btn-danger'>Delete</button>
                      </td>
                  </tr>";
        }
        echo '  </tbody>
              </table>';

    }
    public static function renderFormForUpdate(PDO $dbh) {
        if (isset($_GET['type']) && $_GET['type'] == 'edit') {
            if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
                /** @noinspection SqlResolve */
                $selectQuery = $dbh->prepare("SELECT artist_id, artist_name, description FROM artist WHERE artist_id = ?");
                $selectQuery->bindValue(1, $_GET['id']);
                $selectQuery->execute();

                if ($selectQuery->rowCount() == 1) {
                    $result = $selectQuery->fetchAll(PDO::FETCH_ASSOC);

                    echo "<form action='manageArtists.php' method='post'>
                              <div class=\"row md-3\">
                                  <div class=\"col-3\">
                                      <label for=\"artistName\" class=\"form-label\">Artist</label>
                                      <input id=\"artistName\" name=\"artistName\" type=\"text\" class=\"form-control\" value='".$result[0]['artist_name']."'>
                                      <button type='submit' name='sendArtistsForUpdate' class='btn btn-primary'>Submit</button>
                                  </div>
                                  <div class=\"col-6\">
                                      <label for=\"artistDescription\" class=\"form-label\">Description for artist</label>
                                      <input id=\"artistDescription\" name=\"artistDescription\" type=\"text\" class=\"form-control\" value='".$result[0]['description']."'>
                                      <input type='hidden' id='artistId' name='artistId' value='".$result[0]['artist_id']."'>
                                  </div>
                              </div>
                          </form>";
                }
            }
        }
    }

    public function __construct($dbh, $artistName, $artistDescription) {
        $this->dbh = $dbh;
        $this->errorArray = array();
        $this->messageArray = array();

        $this->artistName = $artistName;
        $this->artistDescription = $artistDescription;
    }

    private function validateArtistName() {
        if (is_array($this->artistName)) {
            for ($i = 0; $i < sizeof($this->artistName); $i++) {
                if (strlen($this->artistName[$i]) < 2 || strlen($this->artistName[$i]) > 45) {
                    array_push($this->errorArray, "Artist ". $i+1 ." name shouldn't have less than 2 characters and more than 45 characters");
                    return;
                }
            }
        }
    }
    private function validateArtistDescription() {
        if (is_array($this->artistDescription)) {
            for ($i = 0; $i < sizeof($this->artistDescription); $i++) {
                if ($this->artistDescription[$i] == NULL) {
                    return;
                }
                if (strlen($this->artistDescription[$i]) < 20 || strlen($this->artistDescription[$i]) > 500) {
                    array_push($this->errorArray, "Artist ". $i+1 ." description shouldn't have less than 20 characters and more than 500");
                    return;
                }
            }
        }
        else {
            if ($this->artistDescription == NULL) {
                return;
            }
            if (strlen($this->artistDescription) < 20 || strlen($this->artistDescription) > 500) {
                array_push($this->errorArray, "Artist description shouldn't have less than 20 characters and more than 500");
                return;
            }
        }
    }

    private function insertQuery(): bool {
        /** @noinspection SqlResolve */
        $query = $this->dbh->prepare('INSERT INTO artist (artist_name, description, user_account_id) VALUES (?, ?, (SELECT user_account_id FROM user_account WHERE login = ?))');

        $this->dbh->beginTransaction();

        for ($i = 0; $i < sizeof($this->artistName); $i++) {
            $query->bindParam(1, $this->artistName[$i]);
            if (empty($this->artistDescription[$i])) {
                $query->bindValue(2, NULL, PDO::PARAM_NULL);
            }
            else {
                $query->bindParam(2, $this->artistDescription[$i]);
            }
            $query->bindParam(3, $_SESSION['userLoggedIn']);
            $query->execute();
        }

        return $this->dbh->commit();
    }
    private function updateQuery(): bool {

        /** @noinspection SqlResolve */
        $query = $this->dbh->prepare("UPDATE artist SET artist_name = ?, description = ? WHERE artist_id = ?");
        $query->bindParam(1, $this->artistName);
        if (empty($this->artistDescription)) {
            $query->bindValue(2, NULL, PDO::PARAM_NULL);
        }
        else {
            $query->bindParam(2, $this->artistDescription);
        }
        $query->bindValue(3, $_POST['artistId']);

        return $query->execute();
    }

    public function insertArtistData(): bool {
        $this->validateArtistName();
        $this->validateArtistDescription();

        if (empty($this->errorArray)) {
            array_push($this->messageArray, "Data inserted");
            return $this->insertQuery();
        }
        else {
            return false;
        }
    }
    public function updateArtistData(): bool {
        $this->validateArtistName();
        $this->validateArtistDescription();

        if (empty($this->errorArray)) {
            array_push($this->messageArray, "Data updated");
            return $this->updateQuery();
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