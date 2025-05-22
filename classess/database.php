<?php

class database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'lms_app'); // Change credentials as needed
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->createAuthorTableIfNotExists();
    }

    private function createAuthorTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS author (
            author_id INT AUTO_INCREMENT PRIMARY KEY,
            author_FN VARCHAR(100) NOT NULL,
            author_LN VARCHAR(100) NOT NULL,
            author_birthday DATE NOT NULL,
            author_nat VARCHAR(100) NOT NULL
        )";
        $this->conn->query($sql);
    }

    function opencon(): PDO {
        return new PDO(dsn: 'mysql:host=localhost;dbname=lms_app',
        username: 'root',
        password: '');   
    }

    function signupUser($firstname, $lastname, $birthday, $email, $sex, $phone, $username, $password, $profile_picture_path) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            // Insert into Users table
            $stmt = $con->prepare("INSERT INTO Users (user_FN, user_LN, user_birthday, user_sex, user_email, user_phone, user_username, user_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password]);

            // Get the newly inserted user_id
            $userId = $con->lastInsertID();

            // Insert into user_pictures table
            $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
            $stmt->execute([$userId, $profile_picture_path]);

            $con->commit();
            return $userId; // return user_id for further use (like inserting address)
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function insertAddress($userID, $street, $barangay, $city, $province) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            // Insert into address table
            $stmt = $con->prepare("INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)");
            $stmt->execute([$street, $barangay, $city, $province]);

            // Get the newly inserted address_id
            $addressId = $con->lastInsertID();

            // Link User and Address into Users_Address table
            $stmt = $con->prepare("INSERT INTO Users_Address (user_id, address_id) VALUES (?, ?)");
            $stmt->execute([$userID, $addressId]);

            $con->commit();
            return true;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function loginUser($email, $password) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM users WHERE user_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['user_password'])) {
            return $user;
        } else {
            return false;
        }
    }

    public function addAuthor($firstname, $lastname, $birthdate, $nationality) {
        $con = $this->opencon();
        try {
            $stmt = $con->prepare("INSERT INTO authors (author_FN, author_LN, author_birthday, author_nat) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$firstname, $lastname, $birthdate, $nationality]);
        } catch (PDOException $e) {
            return false;
        }
    }

    function addGenre($genrename){
        
        $con = $this->opencon();
        
        try{
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO genres (genre_name) VALUES (?)");
            $stmt->execute([$genrename]);

            $genreID = $con->lastInsertId();
            $con->commit();

            return $genreID;

        }catch (PDOException $e){
            $con->rollBack();
            return false;
        }

    }
}

?>