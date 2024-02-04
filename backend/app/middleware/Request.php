<?php

// $_GET['login'] -> query parameter
// { "login": "user"} -> body_parameters parameter
// /api/user/1 -> path parameter

class Request {
    public function __construct(
        public ?Token $token,
        public string $method,
        public ?Endpoint $endpoint,
        public ?int $id,
        public array $query_parameters,
        public array $body_parameters,
        public array $path_parameters,
        public ?Identity $identity
    ) {}

    public function get_query_parameter(string $parameter_name): ?string { 
        if (key_exists($parameter_name, $this->query_parameters))
            return $this->query_parameters[$parameter_name];
        return null;
    }

    public function has_query_parameter(string $parameter_name): bool {
        return key_exists($parameter_name, $this->query_parameters);
    }

    public function get_body_parameter(string $parameter_name): ?string {
        if (key_exists($parameter_name, $this->body_parameters))
            return $this->body_parameters[$parameter_name];
        return null;
    }

    public function has_body_parameter(string $parameter_name): bool {
        return key_exists($parameter_name, $this->body_parameters);
    }

    public function get_path_parameter(string $parameter_name): ?string {
        if (key_exists($parameter_name, $this->path_parameters))
            return $this->path_parameters[$parameter_name];
        return null;
    }

    public function has_path_parameter(string $parameter_name): bool {
        return key_exists($parameter_name, $this->path_parameters);
    }
}