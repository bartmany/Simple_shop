<?php


class Message{
    static private $conn;

    private $id;
    private $senderId;
    private $senderName;
    private $receiverId;
    private $receiverName;
    private $opened;
    private $message;

    public static function SetConnection($newConnection){
        Message::$conn = $newConnection;
    }

    public static function CreateMessage($senderId, $senderName, $receiverId, $receiverName, $message){
        $sqlStatement = "INSERT INTO Messages(sender_id, reciever_id, message) values ($senderId, $receiverId, '$message')";
        if (Message::$conn->query($sqlStatement) === TRUE) {
            return new Message(Message::$conn->insert_id, $senderId, $senderName, $receiverId, $receiverName, $message, false);
        }
        return null;
    }

    public static function DeleteMessage($toDeleteId){
        $sql = "DELETE FROM Messages WHERE id = {$toDeleteId}";
        if (Message::$conn->query($sql) === TRUE) {
            return true;
        }
        return false;
    }

    public static function GetAllRecievedMessages($recieverId, $recieverName, $limit = 0){
        $ret = array();
        $sqlStatement = "select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.sender_id = Users.id where reciever_id = $recieverId";
        if($limit > 0){
            $sqlStatement .= " LIMIT $limit";
        }
        $result = Message::$conn->query($sqlStatement);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $ret[] = new Message($row['id'], $row['sender_id'], $row['name'], $row['reciever_id'], $recieverName, $row['message'], $row['opened']);
            }
        }
        return $ret;
    }

    public static function GetAllSendMessages($senderId, $senderName, $limit = 0){
        $ret = array();
        $sqlStatement = "select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.reciever_id = Users.id where sender_id = $senderId";
        if($limit > 0){
            $sqlStatement .= " LIMIT $limit";
        }
        $result = Message::$conn->query($sqlStatement);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $ret[] = new Message($row['id'], $row['sender_id'], $senderName, $row['reciever_id'], $row['name'], $row['message'], $row['opened']);
            }
        }
        return $ret;
    }

    public function __construct($newId, $senderId, $senderName, $receiverId, $receiverName, $message, $opened = false){
        $this->id = $newId;
        $this->senderId = $senderId;
        $this->senderName = $senderName;
        $this->receiverId = $receiverId;
        $this->receiverName = $receiverName;
        $this->message = $message;
        $this->opened = $opened;
    }
    // @codeCoverageIgnoreStart
    public function getId(){
        return $this->id;
    }

    public function getSenderId(){
        return $this->senderId;
    }

    public function getSenderName(){
        return $this->senderName;
    }

    public function getReceiverId(){
        return $this->receiverId;
    }

    public function getReceiverName(){
        return $this->senderName;
    }

    public function getMessage(){
        return $this->message;
    }
    public function setMessageText($newMessage){
        $this->message = $newMessage;
    }
    
    public function getOpened(){
        return $this->opened;
    }
    public function openMessage(){
        $this->opened = date("Y-m-d H:i:s");
        $this->saveToDB();
    }
    //this function is responsible for saving any changes done to Message to database
    public function saveToDB(){
        $sql = "UPDATE Messages SET opened='{$this->opened}' WHERE id={$this->id}";
        return Message::$conn->query($sql);
    }
    // @codeCoverageIgnoreEnd
    
    
}