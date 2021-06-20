<?php

class Account {

    private $dbh; //db handler
    private $errorArray; // array which contains any errors pushed from validation methods

    private $name;
    private $surname;
    private $username;
    private $password;
    private $passwordConfirmation;

    public function __construct($dbh, $name, $surname, $username, $password, $passwordConfirmation) {
        $this->dbh = $dbh;
        $this->errorArray = array();

        $this->name = $name;
        $this->surname = $surname;
        $this->username = $username;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }
    private function validateName() {
        if (strlen($this->name) > 45 || strlen($this->name) < 2) {
            array_push($this->errorArray, 'Your name must be between 2 and 45 characters.');
        }
    }
    private function validateSurname() {
        if (strlen($this->surname) > 45 || strlen($this->surname) < 2) {
            array_push($this->errorArray, 'Your surname must be between 2 and 45 characters.');
        }
    }
    private function validateUsername() {
        if (strlen($this->username) > 25 || strlen($this->username) < 5) {
            array_push($this->errorArray, 'Your username must be between 5 and 25 characters.');
            return;
        }

        /** @noinspection SqlResolve */
        $query = $this->dbh->prepare("SELECT login FROM user_account WHERE login = ?");
        $query->bindParam(1, $this->username);
        $query->execute();

        if ($query->rowCount() != 0) {
            array_push($this->errorArray, 'This username already exists');
        }
    }
    private function validatePasswords() {
        if ($this->password != $this->passwordConfirmation) {
            array_push($this->errorArray, 'Passwords need to be the same.');
            return;
        }

        if (preg_match("/^(.{0,7}|[^0-9]*|[^A-Z]*|[^a-z]*|[a-zA-Z0-9]*)$/", $this->password)) {
            array_push($this->errorArray, 'Your password needs to contain at least 8 characters, lower and uppercase characters and special signs.');
            return;
        }

        if (strlen($this->password) > 30 || strlen($this->password) < 8) {
            array_push($this->errorArray, 'Your password must be between 8 and 30 characters.');
        }
    }

    private function insertUserData(): bool {
        $encryptedPassword = hash('sha512', $this->password);
        $date = date('Y-m-d');

        /** @noinspection SqlResolve */
        $query = $this->dbh->prepare("INSERT INTO user_account(name, surname, login, password, signUpDate) VALUES (?,?,?,?,?)");
        $query->bindParam(1, $this->name);
        $query->bindParam(2, $this->surname);
        $query->bindParam(3, $this->username);
        $query->bindParam(4, $encryptedPassword);
        $query->bindParam(5, $date);
        return $query->execute();
    }

    public function login(): bool {
        $encryptedPassword = hash('sha512', $this->password);

        /** @noinspection SqlResolve */
        $query = $this->dbh->prepare('SELECT login, password FROM user_account WHERE login = ? AND password = ?');
        $query->bindParam(1, $this->username);
        $query->bindParam(2, $encryptedPassword);
        $query->execute();

        if ($query->rowCount() == 1) {
            return true;
        }
        else {
            array_push($this->errorArray, 'Invalid username or password');
            return false;
        }
    }
    public function register(): bool {
        $this->validateName();
        $this->validateSurname();
        $this->validateUsername();
        $this->validatePasswords();

        if (empty($this->errorArray)) {
            return $this->insertUserData();
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