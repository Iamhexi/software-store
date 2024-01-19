<?php
require_once __DIR__ . '/../../../app/model/User.php';
require_once __DIR__ . '/../../../app/model/PDODatabase.php';

use PHPUnit\Framework\TestCase;

class PDODatabaseTest extends TestCase {

    /**
     * @test
     */
    // public function testExecuteQuerySuccess(): void {
    //     // Mock PDO instance
    //     $pdoMock = $this->createMock(PDO::class);
    //     $pdoMock->method('prepare')->willReturn($this->createMock(PDOStatement::class));
    //     $pdoMock->method('execute')->willReturn(true);
        
    //     // Mock PDODatabase
    //     $pdoDatabase = $this->getMockBuilder(PDODatabase::class)
    //         ->onlyMethods(['create_pdo'])
    //         ->getMock();
    //     $pdoDatabase->method('create_pdo')->willReturn($pdoMock);

    //     // Execute the test
    //     $query = 'INSERT INTO User VALUES (:user_id, :login, :pass_hash, :username, :account_creation_date, :account_type)';
    //     $result = $pdoDatabase->execute_query($query,
    //     params: [
    //         'user_id' => 1,
    //         'login' => 'login',
    //         'pass_hash' => 'hash',
    //         'username' => 'username',
    //         'account_creation_date' => '',
    //         'account_type' => 'author'
    //     ]);

    //     $this->assertTrue($result);
    // }

    // public function testExecuteQueryFailure(): void {
    //     // Mock PDO instance
    //     $pdoMock = $this->createMock(PDO::class);
    //     $pdoMock->method('prepare')->willReturn($this->createMock(PDOStatement::class));
    //     $pdoMock->method('execute')->willThrowException(new PDOException('Query failed'));

    //     // Mock PDODatabase
    //     $pdoDatabase = $this->getMockBuilder(PDODatabase::class)
    //         ->onlyMethods(['create_pdo'])
    //         ->getMock();
    //     $pdoDatabase->method('create_pdo')->willReturn($pdoMock);

    //     // Mock Logger
    //     $loggerMock = $this->createMock(Logger::class);
    //     $loggerMock->expects($this->once())
    //         ->method('log')
    //         ->with('Query failed', Priority::ERROR);

    //     // Set the logger mock
    //     $pdoDatabase::setLogger($loggerMock);

    //     // Execute the test
    //     $query = 'SELECT * FROM table';
    //     $result = $pdoDatabase->execute_query($query);

    //     $this->assertFalse($result);
    // }

    // public function testGetRows(): void {
    //     // Mock PDO instance
    //     $pdoMock = $this->createMock(PDO::class);
    //     $pdoMock->method('prepare')->willReturn($this->createMock(PDOStatement::class));
    //     $pdoMock->method('execute')->willReturn(true);

    //     // Mock PDODatabase
    //     $pdoDatabase = $this->getMockBuilder(PDODatabase::class)
    //         ->onlyMethods(['create_pdo'])
    //         ->getMock();
    //     $pdoDatabase->method('create_pdo')->willReturn($pdoMock);

    //     // Execute the test
    //     $query = 'SELECT * FROM table';
    //     $result = $pdoDatabase->get_rows($query);

    //     $this->assertIsArray($result);
    // }

    // Add more test cases as needed
}
