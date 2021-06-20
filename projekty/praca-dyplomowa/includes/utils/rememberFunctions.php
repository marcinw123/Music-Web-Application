<?php

function rememberNumberOfForms() {
    if (isset($_POST['generateArtistFormButton']) || isset($_POST['generateSongsFormButton'])) {
        echo $_POST['numberOfForms'];
    }
}

function rememberRegisterName() {
    if (isset($_POST['registerButton'])) {
        echo $_POST['registerName'];
    }
}
function rememberRegisterSurname() {
    if (isset($_POST['registerButton'])) {
        echo $_POST['registerSurname'];
    }
}
function rememberRegisterUsername() {
    if (isset($_POST['registerButton'])) {
        echo $_POST['registerUsername'];
    }
}

