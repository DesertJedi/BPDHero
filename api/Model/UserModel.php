<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class UserModel extends Database
{
    public function getUsers($limit)
    {
        return $this->select("SELECT * FROM users ORDER BY id ASC LIMIT ?", ["i", $limit]);
    }

    public function addUser($username,$password,$email)
    {
        if ($this->selectSingleUser($username)){
            return "userAlreadyExists";
        } elseif ($this->selectSingleEmail($email)){
            return "emailAlreadyExists";
        } else {
            $hashedPw = $this->hashPassword($password);
            if($this->insert("INSERT INTO users (username,password,email,created_at,updated_at,app_name) VALUES ('" . $username . "','" . $hashedPw . "','" . $email . "',NOW(),NOW(),'bpd_hero')")){
                return "userAdded";
            } else {
                return false;
            }
        }
    }

    public function selectSingleUser($username){
        if($result = $this->select("SELECT * FROM users WHERE username='".$username."'")){
            return true;
        } else {
            return false;
        }
    }

    public function selectSingleEmail($email){
        if($result = $this->select("SELECT * FROM users WHERE email='".$email."'")){
            return true;
        } else {
            return false;
        }
    }

    public function hashPassword($password){
        $opts04 = [ "cost" => 15, "salt" => "kljf32k234jlk54sdvfjs321oiede" ]; // Maybe move to db
        $hashp04 = password_hash($password, PASSWORD_BCRYPT, $opts04);
        return $hashp04;
    }
}