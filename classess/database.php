<?php
 
class database {
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
            $stmt = $con->prepare("INSERT INTO Users (user_FN, user_LN, user_birthday, user_sex, user_email, user_phone, user_username, user_password) VALUES (?, ?, ?, ? ,? ,?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $birthday, $sex,
            $email, $phone, $username, $password]);
   
            //Get newly inserted user id
            $userId = $con->lastInsertId();
   
            // Insert into users_pictures table
            $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
            $stmt->execute([$userId, $profile_picture_path]);
   
            $con->commit();
            return $userId;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
   
    }//signupUser end
 
    function insertAddress($userID, $street, $barangay, $city, $province) {
        $con = $this->opencon();
        try {
            $con->beginTransaction();
   
            // Insert into Address table
            $stmt = $con->prepare("INSERT INTO Address (ba_street,
            ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)") ;
            $stmt->execute([$street, $barangay, $city, $province]);
   
            // Get the newly inserted address_id
            $addressId = $con->lastInsertId();
 
            //Link User and Address into Users_Address table
            $stmt = $con->prepare("INSERT INTO Users_Address (user_id, address_id) VALUES (?, ?)");
            $stmt->execute([$userID, $addressId]);
 
            $con->commit();
            return true;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }//insertAddress end
}