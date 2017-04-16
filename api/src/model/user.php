<?php
/*
 * Creating User:
 * 1. $user = new User();
 *    $user->setEmail('email')->setUsername('username')->setPasswordHash('password')->save();
 * 
 * 2. $user = new User('email', 'username')->setPasswordHash('password')->save();
 *    $user->setPasswordHash('password')->save();
 */

class User extends activeRecord {

    private $username;
    private $email;
    private $passwordHash;

    public function __construct($email = '', $username = '', $password = '') {
        parent::__construct();
        $this->setEmail($email);
        $this->setUsername($username);
        $this->passwordHash = $password;
    }

    public function save() {
        self::connect();
        if ($this->id == -1) {
            $sql = "INSERT INTO users (username, email, passwordHash) values (:username, :email, :passwordHash)";
            $stmt = self::$db->conn->prepare($sql);
            if (User::loadByEmail($this->getEmail()) == null) {
                $result = $stmt->execute([
                    'username' => $this->username,
                    'email' => $this->email,
                    'passwordHash' => $this->passwordHash
                ]);
                if ($result == true) {
                    $this->id = self::$db->conn->lastInsertId();
                    return true;
                }
            }
            return null;
        } else {
            $this->update();
        }
        return null;
    }

    public function update() {
        $sql = "UPDATE users SET username = :username, email = :email, passwordHash = :passwordHash WHERE id = :id";
        $stmt = self::$db->conn->prepare($sql);
        $result = $stmt->execute([
            'username' => $this->username,
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
            'id' => $this->id
        ]);
        return $result;
    }

    public function delete() {
        self::connect();
        if ($this->id != -1) {
            $sql = "DELETE FROM users WHERE id=:id";
            $stmt = self::$db->conn->prepare($sql);
            $result = $stmt->execute(['id' => $this->id]);
            if ($result == true) {
                $this->id = -1;
                return true;
            }
            return false;
        }
        return true;
    }

    static public function loadById($id) {
        self::connect();
        $sql = "SELECT * FROM users WHERE id=:id";
        $stmt = self::$db->conn->prepare($sql);
        $result = $stmt->execute(['id' => $id]);
        if ($result && $stmt->rowCount() >= 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new user($row['email'], $row['username'], $row['passwordHash']);
            $loadedUser->id = $row['id'];
            return $loadedUser;
        }
        return null;
    }

    static public function loadAll() {
        self::connect();
        $sql = "SELECT * FROM users";
        $result = self::$db->conn->query($sql);
        $returnTable = [];
        if ($result !== false && $result->rowCount() > 0) {
            foreach ($result as $row) {
                $loadedUser = new user($row['email'], $row['username'], $row['passwordHash']);
                $loadedUser->id = $row['id'];
                $returnTable[] = $loadedUser;
            }
        }
        return $returnTable;
    }

    static public function loadByEmail($email) {
        self::connect();
        $sql = "SELECT * FROM users WHERE email=:email";
        $stmt = self::$db->conn->prepare($sql);
        $result = $stmt->execute(['email' => $email]);
        if ($result && $stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new user($row['email'], $row['username'], $row['passwordHash']);
            $loadedUser->id = $row['id'];
            return $loadedUser;
        }
        return null;
    }

    public static function AuthenticateUser($email, $password) {
        $user = User::loadByEmail($email);
        var_dump($user);
        if ($user != null) {
            if ($user->passwordHash == User::createPasswordHash($password)) {
                return $user;
            }
        }
        return null;
    }

    public static function createPasswordHash($password) {
        return md5($password);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setPasswordHash($password) {
        $this->passwordHash = User::createPasswordHash($password);
        return $this;
    }
}
