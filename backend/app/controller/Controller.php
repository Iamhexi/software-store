<?php

abstract class Controller {
    abstract protected function get(): void;
    abstract protected function post(): void;
    abstract protected function put(): void;
    abstract protected function delete(): void;

    public function handle_request(): void {
        match($_SERVER['REQUEST_METHOD']) {
            'GET' => $this->get(),
            'POST' => $this->post(),
            'PUT' => $this->put(),
            'DELETE' => $this->delete()
        };
    }

    public function send_response(int $code, string $message, mixed $data): void {
        $this->set_header();
        http_response_code($code);
        echo json_encode(['code' => $code, 'message' => $message, 'data' => $data]);
        exit();
    }
    
    private function set_header(): void {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
        header('Content-Type: application/json; charset=utf-8');
    }
}