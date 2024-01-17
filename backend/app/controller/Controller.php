<?php
require_once __DIR__.'/../middleware/Request.php';
require_once __DIR__.'/../middleware/Response.php';

abstract class Controller {
    abstract protected function get(Request $request): Response;
    abstract protected function post(Request $request): Response;
    abstract protected function put(Request $request): Response;
    abstract protected function delete(Request $request): Response;

    public function handle_request(Request $request): Response {
        return match($request->method) {
            'get' => $this->get($request),
            'post' => $this->post($request),
            'put' => $this->put($request),
            'delete' => $this->delete($request),
        };
    }

    public static function send_response(int $code, string $message, mixed $data): void {
        self::set_header();
        http_response_code($code);
        echo json_encode(['code' => $code, 'message' => $message, 'data' => $data]);
        exit();
    }
    
    private static function set_header(): void {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
        header('Content-Type: application/json; charset=utf-8');
    }
}