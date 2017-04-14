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

    /**
     * @test test_can_get_user
     * @covers User::GetUser
     * @covers User::SetConnection
     * @covers User::__construct
     */
    public function test_can_get_user() {
        $result = m::mock('result');
        $result->num_rows = 1;
        $result->shouldReceive('fetch_assoc')->andReturn(['id' => 1, 'name' => '1', 'info' => '1', 'email' => '1', 'password' => '1']);

        $expectedUser = new \User(1, '1', '1', '1', '1');

        $db = m::mock('db');
        $db->shouldReceive('query')->withArgs(['Select * from Users where id = \'1\''])->andReturn($result);

        User::SetConnection($db);

        $user = \User::GetUser(1);

        $this->assertEquals($expectedUser, $user);
    }

    /**
     * @test test_can_not_get_user
     * @covers User::GetUser
     * @covers User::SetConnection
     */
    public function test_can_not_get_user() {
        $result = m::mock('result');
        $result->num_rows = 0;

        $db = m::mock('db');
        $db->shouldReceive('query')->withArgs(['Select * from Users where id = \'1\''])->andReturn($result);

        User::SetConnection($db);

        $user = User::GetUser(1);

        $this->assertEquals(-1, $user);
    }

    /**
     * @test test_can_create_user
     * @covers User::CreateUser
     * @covers User::SetConnection
     * @covers User::__construct
     */
    public function test_can_create_user() {
        // Stworzenie mocku wyniku kwerendy
        $result = m::mock('result');
        $result->num_rows = 0;

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // Nadanie propercji insert_id wartosci 2
        $db->insert_id = 2;
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select * from Users where email = \'email\''])->andReturn($result);
        $db->shouldReceive('query')->withArgs(['INSERT INTO Users(name, email, password, info) values (\'\', \'email\', \'c4ca4238a0b923820dcc509a6f75849b\', \'\')'])->andReturn(TRUE);

        // Stworzenie prawdziwego uzytkownika do porwnywania
        $expectedUser = new \User(2, 'jakies', 'email', 'glupoty', md5(1));

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        // Test funkcji
        $user = \User::CreateUser('email', '1');

        // Sprawdzenie wyniku
        $this->assertEquals($expectedUser, $user);
    }

    /**
     * @test test_can_not_create_user
     * @covers User::CreateUser
     * @covers User::SetConnection
     * @covers User::__construct
     */
    public function test_can_not_create_user() {
        // Stworzenie mocku wyniku kwerendy
        $result = m::mock('result');
        $result->num_rows = 1;

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // Nadanie propercji insert_id wartosci 2
        $db->insert_id = 2;
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select * from Users where email = \'email\''])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        // Test funkcji
        $user = \User::CreateUser('email', '1');

        // Sprawdzenie wyniku
        $this->assertNull($user);
    }

    /**
     * @test test_can_authenticate_user
     * @covers User::AuthenticateUser
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     */
    public function test_can_authenticate_user() {
        // Stworzenie mocku wyniku kwerendy
        $result = m::mock('result');
        $result->num_rows = 1;
        $result->shouldReceive('fetch_assoc')->andReturn(['id' => 1, 'name' => 'jakies', 'info' => 'glupoty', 'email' => 'email', 'password' => 'c4ca4238a0b923820dcc509a6f75849b']);

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // Nadanie propercji insert_id wartosci 2
        $db->insert_id = 2;
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select * from Users where email = \'email\''])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        // Stworzenie prawdziwego uzytkownika do porwnywania
        $expectedUser = new \User(1, 'jakies', 'email', 'glupoty', md5(1));

        $user = \User::AuthenticateUser('email', 1);

        $this->assertEquals($expectedUser, $user);
    }

    /**
     * @test test_can_not_authenticate_user
     * @covers User::AuthenticateUser
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     */
    public function test_can_not_authenticate_user() {
        // Stworzenie mocku wyniku kwerendy
        $result = m::mock('result');
        $result->num_rows = 1;
        $result->shouldReceive('fetch_assoc')->andReturn(['id' => 1, 'name' => 'jakies', 'info' => 'glupoty', 'email' => 'email', 'password' => 'c4ca4238a0b923820dcc509a6g75849b']);

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // Nadanie propercji insert_id wartosci 2
        $db->insert_id = 2;
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select * from Users where email = \'email\''])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        // Stworzenie prawdziwego uzytkownika do porwnywania
        $expectedUser = new \User(1, 'jakies', 'email', 'glupoty', md5(1));

        $user = \User::AuthenticateUser('email', 1);

        $this->assertNotEquals($expectedUser, $user);
        $this->assertNull($user);
    }

    /**
     * @test test_can_delete_user
     * @covers User::DeleteUser
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_delete_user() {
        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['DELETE FROM Users WHERE email = \'email\''])->andReturn(TRUE);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $user = new \User(1, 'jakies', 'email', 'glupoty', md5(1));
        $res = \User::DeleteUser($user, 1);

        $this->assertTrue($res);
    }

    /**
     * @test test_can_not_delete_user_by_password
     * @covers User::DeleteUser
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_not_delete_user_by_password() {
        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['DELETE FROM Users WHERE email = \'email\''])->andReturn(TRUE);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $user = new \User(1, 'jakies', 'email', 'glupoty', md5(1));
        $res = \User::DeleteUser($user, 2);

        $this->assertFalse($res);
    }

    /**
     * @test test_can_not_delete_user_by_database
     * @covers User::DeleteUser
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_not_delete_user_by_database() {
        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['DELETE FROM Users WHERE email = \'email\''])->andReturn(FALSE);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $user = new \User(1, 'jakies', 'email', 'glupoty', md5(1));
        $res = \User::DeleteUser($user, 1);

        $this->assertFalse($res);
    }

    /**
     * @test test_can_get_all_users
     * @covers User::GetAllUserNames
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_get_all_users() {
        $retArr = [
            ['id' => 1, 'name' => 'jakies', 'email' => 'email'],
            ['id' => 2, 'name' => 'jakies', 'email' => 'email']
        ];

        $result = m::mock('result');
        $result->num_rows = 2;
        $result
                ->shouldReceive('fetch_assoc')
                ->andReturn($retArr[0], $retArr[1], false);

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select id, name, email from Users'])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $res = \User::GetAllUserNames();

        $this->assertEquals($retArr, $res);
    }

    /**
     * @test test_can_not_get_all_users
     * @covers User::GetAllUserNames
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_not_get_all_users() {
        $result = m::mock('result');
        $result->num_rows = 0;

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select id, name, email from Users'])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $res = \User::GetAllUserNames();

        $this->assertEquals([], $res);
    }

    /**
     * @test test_can_get_user_info
     * @covers User::GetUserInfo
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_get_user_info() {
        $arr = ['id' => 1, 'name' => 'jakies', 'info' => 'glupoty', 'email' => 'email'];
        $result = m::mock('result');
        $result->num_rows = 1;
        $result->shouldReceive('fetch_assoc')->andReturn($arr);

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select id, name, email, info from Users where id=1'])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $res = \User::GetUserInfo(1);

        $this->assertEquals($arr, $res);
    }

    /**
     * @test test_can_not_get_user_info
     * @covers User::GetUserInfo
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::authenticate
     * @covers User::getEmail
     */
    public function test_can_not_get_user_info() {
        $result = m::mock('result');
        $result->num_rows = 0;

        // Stworzenie mocku połączenia do bazy danych
        $db = m::mock('db');
        // W przypadku kiedy na objekcie $db zostanie wywołana funkcja 'query' z argumentem jak poniej funkcja ma zwrcic wynik z funkcji 'andReturn'
        $db->shouldReceive('query')->withArgs(['Select id, name, email, info from Users where id=1'])->andReturn($result);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $res = \User::GetUserInfo(1);

        $this->assertNull($res);
    }

    /**
     * @test test_save_to_db
     * @covers User::SetConnection
     * @covers User::__construct
     * @covers User::saveToDB
     */
    public function test_save_to_db() {

        $db = m::mock('db');
        $db->shouldReceive('query')->withArgs(["UPDATE Users SET name='1', email='1', info='1', password='1' WHERE id=1"])->andReturn(true);

        // Dependency injection dla mocku połączenia z bazą danych
        User::SetConnection($db);

        $user = new \User(1, '1', '1', '1', 1);
        
        $this->assertTrue($user->saveToDB());
    }

}
