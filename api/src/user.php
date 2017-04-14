<?php

class User{
    static private $conn;
 
    private $name;
    private $id;
    private $email;
    private $info;
    private $password;
 
    // This function sets connection for this class to use
    // This function needs to be run on startup
    public static function SetConnection($newConnection){
        User::$conn = $newConnection;
    }
 
    //this function returns:
    //  null id user with given id is not in db
    //  User loaded from db if id is ok
    public static function GetUser($id){
        $sqlStatement = "Select * from Users where id = '$id'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 1) {
            $userData = $result->fetch_assoc();
            return new User($userData['id'], $userData['name'], $userData['info'], $userData['email'], $userData['password']);
        }
        //there is user with this name in db
        return -1;
    }
 
    //this function returns:
    //   null if user exist in database
    //   new User object if new entry was added to table
    public static function CreateUser($userMail, $password){
        $sqlStatement = "Select * from Users where email = '$userMail'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 0) {
            //inserting user to db
            $hashed_password = md5($password);
            $sqlStatement = "INSERT INTO Users(name, email, password, info) values ('', '$userMail', '$hashed_password', '')";
            if (User::$conn->query($sqlStatement) === TRUE) {
                //entery was added to DB so we can return new object
                return new User(User::$conn->insert_id, 'jakies', $userMail, 'glupoty', $hashed_password);
            }
        }
        //there is user with this name in db
        return null;
    }
 
    //this function returns:
    //   null if user does not exist in database or password does not match
    //   new User object if User was authenticated
    public static function AuthenticateUser($userMail, $password){
        $sqlStatement = "Select * from Users where email = '$userMail'";
        $result = User::$conn->query($sqlStatement);
        if ($result->num_rows == 1) {
            $userData = $result->fetch_assoc();
            $user = new User($userData['id'], $userData['name'], $userData['email'], $userData['info'], $userData['password']);
 
            if($user->authenticate($password)){
                //User is authenticated - we can return him
                return $user;
            }
        }
        //there is no user with this name in db or User was not authenticated
        return null;
    }
 
    //this function return:
    //   true if user was deleted
    //   false if not
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
 
    //this function is responsible for saving any changes done to User to database
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