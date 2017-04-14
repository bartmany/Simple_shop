<?php
// ./vendor/bin/phpunit ./tests/* --coverage-html=coverage

namespace PHPUnit\Framework;

class orderTest extends TestCase {
  private $testObj;

    protected function setUp(){
      $this->testObj = new \Order(2,9,3);
      parent::setUp();
    }

    protected function tearDown(){
      $this->testObj = null;
      parent::tearDown();
    }
    /**
     * @test if_is_created_correctly
     * @covers Order::__construct
     * @covers Order::getId
     * @covers Order::getUserId
     * @covers Order::getProductId
     * @covers Order::getProductQuantity
     */
    public function if_is_created_correctly(){
      $this->assertEquals(-1,$this->testObj->getId());
      $this->assertEquals(2,$this->testObj->getUserId());
      $this->assertEquals(9,$this->testObj->getProductId());
      $this->assertEquals(3,$this->testObj->getProductQuantity());
    }
  }
