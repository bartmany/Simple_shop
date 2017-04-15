<?php
class Order {
  private $id;
  private $userId;
  private $productId;
  private $productQuantity;
  private $totalPrice;

    public static function SetConnection($newConnection){
        Order::$conn = $newConnection;
    }

    public function __construct($userId, $productId, $productQuantity)
    {
        $this->id = -1;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->productQuantity = $productQuantity;
    }

    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    // @codeCoverageIgnoreEnd

    public function saveToDB(){
        //$sql = ;
        return Order::$conn->query($sql);
    }

}
