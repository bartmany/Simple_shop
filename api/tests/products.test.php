<?php
// ./vendor/bin/phpunit ./tests/* --coverage-html=coverage

namespace PHPUnit\Framework;

class productTest extends TestCase {
  private $testObj;

    protected function setUp(){
      $this->testObj = new \Product(1,2,3,4);
      parent::setUp();
    }

    protected function tearDown(){
      $this->testObj = null;
      parent::tearDown();
    }

    /**
     * @test if_is_created_correctly
     * @covers Product::getStockQty
     * @covers Product::getUnitPrice
     * @covers Product::getProductCode
     * @covers Product::getId
     * @covers Product::__construct
     */
    public function if_is_created_correctly(){
      $this->assertEquals(4,$this->testObj->getStockQty());
      $this->assertEquals(3,$this->testObj->getUnitPrice());
      $this->assertEquals(2,$this->testObj->getProductCode());
      $this->assertEquals(1,$this->testObj->getId());
    }

    /**
     * @test if_is_sold_and_buyed_correctly
     * @covers Product::getStockQty
     * @covers Product::sell
     * @covers Product::buy
     * @covers Product::__construct
     */
    public function if_is_sold_and_buyed_correctly(){
      $this->testObj->sell(1);
      $this->assertEquals(3,$this->testObj->getStockQty());
      $this->testObj->buy(2);
      $this->assertEquals(5,$this->testObj->getStockQty());
    }

    /**
     * @test if_is_price_calculated_correctly
     * @covers Product::getPriceForQuantity
     * @covers Product::getStockQty
     * @covers Product::__construct
     */
    public function if_is_price_calculated_correctly(){
      $this->assertEquals(12,$this->testObj->getPriceForQuantity($this->testObj->getStockQty()));
    }

    /**
     * @test if_is_on_stock
     * @covers Product::hasStock
     * @covers Product::sell
     * @covers Product::__construct
     */
    public function if_is_on_stock(){
      $this->assertTrue($this->testObj->hasStock());
      $this->testObj->sell(5);
      $this->assertFalse($this->testObj->hasStock());
    }
  }
