<?php

abstract class Controller {
    abstract protected function get(Request $request): void;
    abstract protected function post(Request $request): void;
    abstract protected function put(Request $request): void;
    abstract protected function delete(Request $request): void;

    public function handle_request(Request $request): void {
        match($request->method) {
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