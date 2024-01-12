<?php

// User test with PHPUnit

require_once __DIR__ . '/../../../app/model/AccountType.php';
require_once __DIR__ . '/../../../app/model/User.php';
require_once __DIR__ . '/../../../app/Config.php';
require_once __DIR__ . '/../../../app/model/AccountChangeRequest.php';


use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User(
            user_id: 1,
            login: 'UserLogin',
            pass_hash: password_hash('password123', Config::HASHING_ALGORITHM),
            username: 'UserName',
            account_creation_date: new DateTime(),
            account_type: AccountType::CLIENT
        );
    }

    /** @test
     *  @dataProvider accountTypesToTests
     */
    public function testChangeAccountType(string $accountType): void
    {
        $newAccountType = AccountType::fromString($accountType);

        $this->user->change_account_type($newAccountType);

        $this->assertSame($newAccountType, $this->user->account_type);
    }

    /** @test */
    public function testValidatePassword(): void
    {
        $this->assertTrue($this->user->validate_password('password123'));
        $this->assertFalse($this->user->validate_password('wrong_password'));
    }

    /** @test */
    // public function testGenerateAccountChangeRequestByClient(): void
    // {
    //     // Mock DateTime to ensure consistent testing of date_submitted
    //     $dateTime = new DateTime();

    //     $description = 'Changing account type';

    //     // Expect an AccountChangeRequest to be created with the correct parameters
    //     $expectedRequest = new AccountChangeRequest(
    //         request_id: null,
    //         user_id: 1,
    //         description: 'Changing account type',
    //         date_submitted: $dateTime,
    //         review_status: RequestStatus::Pending
    //     );

    //     //TODO: CHECK DATE WITH DELTA IN OBJECT ???
    //     $this->assertEquals($expectedRequest, $this->user->generate_account_change_request($description));
    // }

    /** @test 
     *  @dataProvider nonClientAccountTypesToTests
    */
    public function testGenerateAccountChangeRequestByNonClient(AccountType $acc_type): void
    {
        $this->user->account_type = $acc_type;
        $description = 'Changing account type';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Only the clients may request a change to their account type.");

        $this->user->generate_account_change_request($description);
    }

    public function testMagicMethods(): void
    {
        $this->assertEquals(1, $this->user->__get('user_id'));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Property invalid_property does not exist");
        $this->user->__get('invalid_property');
    }

    public function testToString(): void
    {
        $this->assertEquals('User: UserName', $this->user->__toString());
    }


    public static function accountTypesToTests(): array{
        return [
            ['author'], ['administrator'], ['client']
        ];
    }

    public static function nonClientAccountTypesToTests(): array{
        return [
            [AccountType::ADMIN], [AccountType::SOFTWARE_AUTHOR]
        ];
    }
}
