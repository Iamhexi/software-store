<?php
require_once __DIR__ . '/../../../app/controller/UserController.php';
require_once __DIR__ . '/../../../app/model/repository/UserRepository.php';
require_once __DIR__ . '/../../../app/model/AccountType.php';

use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {


    /**
     * @test
     */
    // public function testGetAllUsers(): void {
    //     $userRepositoryMock = $this->createMock(UserRepository::class);

    //     $userController = new UserController($userRepositoryMock);

    //     $users = [
    //         new User(1, 'user1', 'password1', 'User One', new DateTime(), AccountType::CLIENT),
    //         new User(2, 'user2', 'password2', 'User Two', new DateTime(), AccountType::SOFTWARE_AUTHOR),
    //         new User(3, 'user3', 'password3', 'User Three', new DateTime(), AccountType::CLIENT)
    //     ];

    //     $userRepositoryMock->expects($this->once())
    //         ->method('findAll')
    //         ->willReturn($users);


    //     $userController->get();
    //     $this->expectOutputString(json_encode(['code' => 200,'status' => 'Success', 'data' => $users]));
    // }
}
