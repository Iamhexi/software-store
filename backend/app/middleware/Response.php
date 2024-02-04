<?php

class Response {
    public int $code;
    public string $message;
    public mixed $data;

    public function __construct(int $code, string $message, mixed $data) {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString(): string {
        return json_encode(['code' => $this->code, 'message' => $this->message, 'data' => $this->data]);
    }
}