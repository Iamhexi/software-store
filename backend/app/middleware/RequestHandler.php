<?php
require_once __DIR__ . '/Endpoint.php';
require_once __DIR__ . '/Request.php';

class RequestHandler {
    public static function get_request(): Request {
        $authentication = new AuthenticationService;

        $method = self::get_request_method();
        $endpoint = self::get_endpoint();

        // if its not loging AND registering
        if (!($method === 'post' && $endpoint === Endpoint::Auth) && !($method === 'post'&& $endpoint === Endpoint::User)) {
        $token_bearer = self::get_token_bearer(); // as text
        $token_bearer = $authentication->instantiate_token($token_bearer); // as Token object

        $identity = $authentication->get_indentity($token_bearer);
        }

        $id = self::get_request_id();
        $query_parameters = self::get_request_query();
        $body_parameters = self::get_request_body();
        $path_parameters = self::get_path_parameters();

        return new Request($token_bearer, $method, $endpoint, $id, $query_parameters, $body_parameters, $path_parameters, $identity);
    }
    
    public static function get_request_method(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    // [0 => endpoint, 1 => id] for /api/endpoint/1
    public static function get_path_parameters(): array {
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if ($path === [])
            return [];

        $path_parameters = [];
        $order = -2;

        foreach ($path as $key_value) {
            if ($order >= 0)
                $path_parameters[$order] = $key_value;
            $order++;
        }
        return $path_parameters;
    }

    private static function get_token_bearer(): ?string {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }
        return null;
    }

    public static function get_request_uri(): string {
        return $_SERVER['REQUEST_URI'];
    }

    public static function get_endpoint(): ?Endpoint {
        // $pathWithoutQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $path = explode('/', $_SERVER['REQUEST_URI'])[2] ?? null;
        return $path === null ? null : Endpoint::fromString($path);
    }

    public static function has_query_parameters(string $parameterName): bool { // JSON body vs $_GET[] vs $_POST[] vs 
        return isset($_GET[$parameterName]) && !empty($_GET[$parameterName]);
    }

    public static function get_request_body(): array {
       $body = json_decode(file_get_contents('php://input'), true);
       return $body ?? [];
    }

    public static function get_request_id(): ?int {
        $id = explode('/', $_SERVER['REQUEST_URI'])[3] ?? null;
        return $id === null || !is_numeric($id) ? null : intval($id);
    }

    public static function get_request_query(): array {
        return $_GET;
    }

    public static function get_query_parameter(string $key): ?string {
        $query = explode('?', $_SERVER['REQUEST_URI'])[1] ?? null;
        if ($query === null)
            return null;
        $query = explode('&', $query);
        foreach ($query as $key_value) {
            $key_value = explode('=', $key_value);
            if ($key_value[0] === $key)
                return $key_value[1];
        }
        return null;
    }
}