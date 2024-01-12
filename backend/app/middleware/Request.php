<?php

class Request {
    public static function get_request_method(): string {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public static function get_request_uri(): string {
        return $_SERVER['REQUEST_URI'];
    }

    public static function get_endpoint(): ?Endpoint {
        // TODO: remove query params from the path!
        $path = explode('/', $_SERVER['REQUEST_URI'])[2] ?? null;
        return $path === null ? null : Endpoint::fromString($path);
    }

    public static function has_query_parameters(string $parameterName): bool {
        return isset($_GET['parameterName']) && !empty($_GET['parameterName']);
    }

    public static function get_request_body(): array {
        return json_decode(file_get_contents('php://input'), true);
    }

    public static function get_request_id(): ?int {
        $id = explode('/', $_SERVER['REQUEST_URI'])[3] ?? null;
        return $id === null || !is_numeric($id) ? null : intval($id);
    }

    public static function get_request_query(): array {
        $query = explode('?', $_SERVER['REQUEST_URI'])[1] ?? null;
        if ($query === null)
            return [];
        $query = explode('&', $query);
        $query_array = [];
        foreach ($query as $key_value) {
            $key_value = explode('=', $key_value);
            $query_array[$key_value[0]] = $key_value[1];
        }
        return $query_array;
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