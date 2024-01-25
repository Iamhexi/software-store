<?php
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/../model/repository/TokenRepository.php';
require_once __DIR__ . '/../model/Token.php';

class AuthenticationService {
    private UserRepository $user_repository;
    private TokenRepository $token_repository;

    public function __construct() {
        $this->user_repository = new UserRepository;
        $this->token_repository = new TokenRepository;
    }

    public function instantiate_token(?string $textualToken): ?Token{
        if ($textualToken === null)
            return null;
        return $this->token_repository->find($textualToken);
    }
    
    public function verify_token(Token $token): bool {
        $token = $this->token_repository->find($token->token);
        if ($token === null || !$token->is_valid()) 
            return false;
        else
            return true;
        }

    public function authenticate(string $login, string $password): false|string {
        $user = $this->user_repository->find_by(['login' => $login]);
        
        if ($user === [])
            return false;

        if (count($user) > 1)
            Logger::log("More than one user with the same login exists. One of them have already tried to sign in", Priority::WARNING);

        $user = $user[0];

        if ($user->validate_password($password)) {
            $token = $this->generate_token($user);
            $this->token_repository->save($token);
            return $token->token;
        } else {
            return false;
        }
    }

    public function get_indentity(Token $token): ?Identity {
        $user = $this->user_repository->find($token->user_id);
        if ($user === null)
            return null;
        return new Identity($user->user_id, $user->account_type);
    }

    private function generate_token(User $user): Token {
        return new Token(
            token: bin2hex(random_bytes(Config::AUTH_TOKEN_LENGTH/2)),
            user_id: $user->user_id,
            expires_at: new DateTime("@" . strval(time() + Config::EXPIRATION_TIME_IN_SECONDS))
        );
    }
}