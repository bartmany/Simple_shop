<?php

use \Mockery as m;
use PHPUnit\Framework\TestCase;

//use PHPUnit\DbUnit\TestCaseTrait;

class userTest extends TestCase {

    protected function setUp() {
        parent::setUp();
    }

    protected function tearDown() {
        parent::tearDown();
        m::close();
    }

}
