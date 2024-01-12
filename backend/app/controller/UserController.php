<?php
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/Controller.php';

class UserController extends Controller {

    private UserRepository $user_repository;

    public function __construct() {
        $this->user_repository = new UserRepository;
    }

    private function exists(mixed $value): bool {
        return isset($value) && !empty($value);
    }

    private function isCorrectPrimaryKey(mixed $key) : bool {
        return $this->exists($key) && is_numeric($key) && $key >= 0;
    }

    protected function get(Request $request): void {
        $id = $request->id;
        if (!$this->exists($id))
            self::send_response(200, 'Success', $this->user_repository->findAll());
        else if (!$this->isCorrectPrimaryKey($id))
            self::send_response(400, 'Failure', 'Invalid id');
        else {
            $user = $this->user_repository->find($id);
            if ($user === null)
                self::send_response(404, 'Failure', 'User not found');
            else
                self::send_response(200, 'Success', $user);
        }
    }

    protected function post(Request $request): void {
        $data = $request->body_parameters;
        if (!$this->exists($data['login']) || !$this->exists($data['password']) || !$this->exists($data['username']) || !$this->exists($data['account_type']))
            self::send_response(400, 'Failure', 'Missing data');
        else {
            $password_hash = password_hash($data['password'], Config::HASHING_ALGORITHM);
            $user = new User(
                user_id: null,
                login: $data['login'],
                pass_hash: $password_hash,
                username: $data['username'],
                account_type: AccountType::fromString($data['account_type']),
                account_creation_date: new DateTime()
            );
            if ($this->user_repository->save($user))
                self::send_response(201, 'Success', 'User created');
            else
                self::send_response(500, 'Failure', 'Could not create a new user. Internal error. Possibly a duplicate login.');
        }
    }

    protected function put(Request $request): void {
        $data = $request->body_parameters;
        if (!$this->exists($data['login']) || !$this->exists($data['password']) || !$this->exists($data['username']) || !$this->exists($data['account_creation_date']) || !$this->exists($data['account_type']))
            self::send_response(400, 'Failure', 'Missing data');
        else {
            $login = $data['login'];
            $password_hash = password_hash($data['password'], Config::HASHING_ALGORITHM);
            $user = new User(
                login: $login ,
                pass_hash: $password_hash,
                username: $data['username'],
                account_type: new AccountType($data['account_type'])
            );
            if ($this->user_repository->save($user))
                self::send_response(201, 'Success', 'User created');
            else
                self::send_response(500, 'Failure', "Could not update the requested user with login: $login");
        }
    }

    protected function delete(Request $request): void {
        $id = $request->id;
        if (!$this->exists($id) || !is_numeric($id))
            self::send_response(400, 'Failure', 'Missing or invalid id');
        if ($this->user_repository->find($id) === null)
            self::send_response(500, 'Failure', "User with id = $id does not exist, thus, cannot be deleted" );
        else if ($this->user_repository->delete($id)) {
            self::send_response(200, 'Success', 'User deleted');
        }
        else
            self::send_response(500, 'Failure', 'Could not delete the requested user with id: ' . $id);
    }


}