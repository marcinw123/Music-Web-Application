<?php
require 'includes/handlers/addNewArtistHandler.php';
require 'includes/utils/registerHeader.php';
require 'includes/utils/rememberFunctions.php'
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Music App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<div class="container-fluid h-100" style="color: #dcdcdc">
    <div class="row h-100">
        <div class="col-2 sticky-top" style="background-color: black">
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="search.php">Search</a>
                <a class="nav-link" href="addNewArtist.php">Add new artist</a>
                <a class="nav-link" href="manageArtists.php">Manage your artists</a>
                <a class="nav-link" href="addNewSongs.php">Add new songs</a>
                <a class="nav-link" href="manageSongs.php">Manage your songs</a>
                <a class="nav-link" href="logout.php">Log out</a>
            </nav>
        </div>
        <div class="col-10 h-100 sticky-top overflow-auto" style="background-color: #343434">
            <div class="container-fluid">
                <form action="addNewArtist.php" method="post">
                    <h1>Add new artists to database</h1>
                    <div class="row md-3">
                        <div class="col-4">
                            <label for="numberOfForms" class="form-label">Number of artists</label>
                            <input id="numberOfForms" name="numberOfForms" type="number" class="form-control w-50" value="<?php rememberNumberOfForms(); ?>" required>
                            <button type="submit" name="generateArtistFormButton" class="btn btn-primary">Generate</button>
                        </div>
                    </div>
                    <?php if (isset($artist)) $artist->returnAllMessages(); ?>
                    <?php Artist::renderForm(); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous">
    </body>
    </html>
