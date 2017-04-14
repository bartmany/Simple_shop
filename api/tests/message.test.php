<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase {

    protected function setUp() {
        parent::setUp();
    }

    protected function tearDown() {
        parent::tearDown();
        m::close();
    }

    /**
     * @test test_can_create_message
     * @covers Message::CreateMessage
     * @covers Message::SetConnection
     */
    public function test_can_create_message() {

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->withArgs(["INSERT INTO Messages(sender_id, reciever_id, message) values (1, 2, 'message')"])
                ->andReturn(false);

        Message::SetConnection($db);

        $message = Message::CreateMessage(1, 'user', 2, 'user2', 'message');

        $this->assertNull($message);
    }

    /**
     * @test test_can_not_create_message
     * @covers Message::CreateMessage
     * @covers Message::SetConnection
     *  @covers Message::__construct
     */
    public function test_can_not_create_message() {

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->withArgs(["INSERT INTO Messages(sender_id, reciever_id, message) values (1, 2, 'message')"])
                ->andReturn(true);
        $db->insert_id = 1;

        Message::SetConnection($db);

        $expectedMessage = new Message(1, 1, 'user', 2, 'user2', 'message');
        $message = Message::CreateMessage(1, 'user', 2, 'user2', 'message');

        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * @test test_can_not_delete_message
     * @covers Message::DeleteMessage
     * @covers Message::SetConnection
     */
    public function test_can_not_delete_message() {
        $db = m::mock('db');
        $db->shouldReceive('query')
                ->withArgs(["DELETE FROM Messages WHERE id = 1"])->andReturn(false);
        Message::SetConnection($db);
        $this->assertFalse(Message::DeleteMessage(1));
    }

    /**
     * @test test_can_delete_message
     * @covers Message::DeleteMessage
     * @covers Message::SetConnection
     */
    public function test_can_delete_message() {
        $db = m::mock('db');
        $db->shouldReceive('query')
                ->withArgs(["DELETE FROM Messages WHERE id = 1"])->andReturn(true);
        Message::SetConnection($db);
        $this->assertTrue(Message::DeleteMessage(1));
    }

    /**
     * @test test_can_get_all_recieved_messages
     * @covers Message::GetAllRecievedMessages
     * @covers Message::SetConnection
     * @covers Message::__construct
     */
    public function test_can_get_all_recieved_messages() {
        $retArr = [
            ['id' => 1, 'sender_id' => 1, 'name' => 'user', 'reciever_id' => 2, 'message' => 'message', 'opened' => true],
            ['id' => 2, 'sender_id' => 1, 'name' => 'user', 'reciever_id' => 2, 'message' => 'message2', 'opened' => false]
        ];

        $result = m::mock('result');
        $result->num_rows = 2;
        $result->shouldReceive('fetch_assoc')->andReturn($retArr[0], false);

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->with("select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.sender_id = Users.id where reciever_id = 1 LIMIT 1")
                ->andReturn($result);

        Message::SetConnection($db);

        $messageObj1 = new Message(1, 1, 'user', 2, 'user2', 'message', true);
        $expectedReturn = [$messageObj1];

        $this->assertEquals($expectedReturn, Message::GetAllRecievedMessages(1, 'user2', 1));
    }

    /**
     * @test test_can_not_get_all_recieved_messages
     * @covers Message::GetAllRecievedMessages
     * @covers Message::SetConnection
     */
    public function test_can_not_get_all_recieved_messages() {
        $result = m::mock('result');
        $result->num_rows = 0;

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->with("select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.sender_id = Users.id where reciever_id = 1")
                ->andReturn($result);

        Message::SetConnection($db);

        $this->assertEquals([], Message::GetAllRecievedMessages(1, 'user'));
    }

    /**
     * @test test_can_get_all_send_messages
     * @covers Message::GetAllSendMessages
     * @covers Message::SetConnection
     * @covers Message::__construct
     */
    public function test_can_get_all_send_messages() {
        $retArr = [
            ['id' => 1, 'sender_id' => 1, 'name' => 'user2', 'reciever_id' => 2, 'message' => 'message', 'opened' => true],
            ['id' => 2, 'sender_id' => 1, 'name' => 'user2', 'reciever_id' => 2, 'message' => 'message2', 'opened' => false]
        ];

        $result = m::mock('result');
        $result->num_rows = 2;
        $result->shouldReceive('fetch_assoc')->andReturn($retArr[0], false);

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->with("select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.reciever_id = Users.id where sender_id = 1 LIMIT 1")
                ->andReturn($result);

        Message::SetConnection($db);

        $messageObj1 = new Message(1, 1, 'user', 2, 'user2', 'message', true);
        $expectedReturn = [$messageObj1];

        $this->assertEquals($expectedReturn, Message::GetAllSendMessages(1, 'user', 1));
    }

    /**
     * @test test_can_not_get_all_send_messages
     * @covers Message::GetAllSendMessages
     * @covers Message::SetConnection
     */
    public function test_can_not_get_all_send_messages() {
        $result = m::mock('result');
        $result->num_rows = 0;

        $db = m::mock('db');
        $db->shouldReceive('query')
                ->with("select Messages.id, sender_id, name, reciever_id, opened, message from Messages join Users on Messages.reciever_id = Users.id where sender_id = 1")
                ->andReturn($result);

        Message::SetConnection($db);

        $this->assertEquals([], Message::GetAllSendMessages(1, 'user'));
    }

    /**
     * @test test_save_to_db
     * @covers Message::SetConnection
     * @covers Message::__construct
     * @covers Message::saveToDB
     * @covers Message::getOpened
     */
    public function test_save_to_db() {

        $db = m::mock('db');

        Message::SetConnection($db);

        $message = new Message(1, 1, 'user', 1, 'user2', 'message', '2000-01-01 00:00:00');

        $db->shouldReceive('query')
                ->withArgs(["UPDATE Messages SET opened='{$message->getOpened()}' WHERE id=1"])->andReturn(true);

        $this->assertTrue($message->saveToDB());
    }

    /**
     * @test test_save_to_db
     * @covers Message::openMessage
     * @covers Message::__construct
     * @covers Message::saveToDB
     * @covers Message::getOpened
     * @covers Message::SetConnection
     */
    public function test_open_message() {
        $message = new Message(1, 1, 'user', 1, 'user2', 'message');
        $db = m::mock('db');

        Message::SetConnection($db);
        $db->shouldReceive('query')
                ->withArgs([m::any()])->andReturn(true);
        $res = $message->openMessage();
        $this->assertTrue($res);
    }

}
