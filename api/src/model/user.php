<?php

class User{
    static private $conn;
 
    private $name;
    private $id;
    private $email;
    private $info;
    private $password;
 
    public static function SetConnection($newConnection){
        User::$conn = $newConnection;
    }
 
    public static function GetUser($id){
        $sqlStatement = "Select * from Users where id = '$id'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 1) {
            $userData = $result->fetch_assoc();
            return new User($userData['id'], $userData['name'], $userData['info'], $userData['email'], $userData['password']);
        }
        return -1;
    }
 
    public static function CreateUser($userMail, $password){
        $sqlStatement = "Select * from Users where email = '$userMail'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 0) {
            $hashed_password = md5($password);
            $sqlStatement = "INSERT INTO Users(name, email, password, info) values ('', '$userMail', '$hashed_password', '')";
            if (User::$conn->query($sqlStatement) === TRUE) {
                return new User(User::$conn->insert_id, 'jakies', $userMail, 'glupoty', $hashed_password);
            }
        }
        return null;
    }
 
    public static function AuthenticateUser($userMail, $password){
        $sqlStatement = "Select * from Users where email = '$userMail'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 1) {
            $userData = $result->fetch_assoc();
            $user = new User($userData['id'], $userData['name'], $userData['email'], $userData['info'], $userData['password']);
 
            if($user->authenticate($password)){
                return $user;
            }
        }
        return null;
    }
 
    public static function DeleteUser(User $toDelete, $password){
        if($toDelete->authenticate($password)){
            $userMail = $toDelete->getEmail();
            $sql = "DELETE FROM Users WHERE email = '$userMail'";
            if (User::$conn->query($sql) === TRUE) {
                return true;
            }
        }
        return false;
    }
 
    public static function GetAllUserNames(){
        $ret = array();
        $sqlStatement = "Select id, name, email from Users";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $ret[] = $row;
            }
        }
        return $ret;
    }
 
    public static function GetUserInfo($id){
        $sqlStatement = "Select id, name, email, info from Users where id=$id";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
 
    public function __construct($newId, $newName, $newMail, $newInfo, $password){
        $this->id = $newId;
        $this->name = $newName;
        $this->email = $newMail;
        $this->info = $newInfo;
        $this->password = $password;
    }
    // @codeCoverageIgnoreStart
    public function getId(){
        return $this->id;
    }
 
    public function getName(){
        return $this->name;
    }
 
    public function setName($newName){
        $this->name = $newName;
    }
 
    public function getEmail(){
        return $this->email;
    }
 
    public function setEmail($newEmail){
        $this->email = $newEmail;
    }
 
    public function getInfo(){
        return $this->info;
    }
 
    public function setInfo($newInfo){
        $this->info = $newInfo;
    }
 
    public function setPassword($newPassword){
        $this->password = md5($newPassword);
    }
    // @codeCoverageIgnoreEnd
 
    public function saveToDB(){
        $sql = "UPDATE Users SET name='{$this->name}', email='{$this->email}', info='{$this->info}', password='{$this->password}' WHERE id={$this->id}";
        return User::$conn->query($sql);
    }
 
    public function authenticate($password){
        if(md5($password) == $this->password){
            //User is verified
            return true;
        }
        return false;
    }
}