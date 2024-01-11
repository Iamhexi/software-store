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

    public function get(): void {
        $id = (int) $_GET['id'];
        if (!$this->exists($id))
            $this->send_response(200, 'Success', $this->user_repository->findAll());
        else if (!$this->isCorrectPrimaryKey($id))
            $this->send_response(400, 'Failure', 'Invalid id');
        else {
            $user = $this->user_repository->find($id);
            if ($user === null)
                $this->send_response(404, 'Failure', 'User not found');
            else
                $this->send_response(200, 'Success', $user);
        }
        // PHP won't encode object's private properties to JSON unless implement JsonSerializable interface
    }

    public function post(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$this->exists($data['login']) || !$this->exists($data['password']) || !$this->exists($data['username']) || !$this->exists($data['account_creation_date']) || !$this->exists($data['account_type']))
            $this->send_response(400, 'Failure', ['error' => 'Missing data']);
        else {
            $password_hash = password_hash($data['password'], Config::HASHING_ALGORITHM);
            $user = new User(
                login: $data['login'],
                pass_hash: $password_hash,
                username: $data['username'],
                account_type: new AccountType($data['account_type'])
            );
            if ($this->user_repository->save($user))
                $this->send_response(201, 'Success', ['data' => 'User created']);
            else
                $this->send_response(500, 'Failure', ['error' => 'Internal server error']);
        }
    }

    public function put(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$this->exists($data['login']) || !$this->exists($data['password']) || !$this->exists($data['username']) || !$this->exists($data['account_creation_date']) || !$this->exists($data['account_type']))
            $this->send_response(400, 'Failure', 'Missing data');
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
                $this->send_response(201, 'Success', 'User created');
            else
                $this->send_response(500, 'Failure', "Could not create the requested user with login: $login");
        }
    }

    public function delete(): void {
        $id = $_GET['id'];
        if (!$this->exists($id) || !is_numeric($id))
            $this->send_response(400, 'Failure', 'Missing or invalid id');
        else if ($this->user_repository->delete($id))
            $this->send_response(200, 'Success', 'User deleted');
        else
            $this->send_response(500, 'Failure', 'Could not delete the requested user with id: ' . $id);
    }


}