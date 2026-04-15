<?php

namespace App\Core;

class Request
{
    private array $data = [];

    public function __construct()
    {
        $this->parse();
    }

    private function parse(): void
    {
        // Parse GET params
        $this->data = $_GET;

        // Parse POST/PUT JSON payload or Form data
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
            if (strpos($contentType, 'application/json') !== false) {
                // JSON Data
                $input = file_get_contents('php://input');
                $json = json_decode($input, true);
                if (is_array($json)) {
                    $this->data = array_merge($this->data, $json);
                }
            } else {
                // Form data
                $this->data = array_merge($this->data, $_POST);
            }
        }
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(): string
    {
        $uri = $_GET['_url'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Fallback for subfolder deployments
        $uri = str_replace('/public', '', $uri);
        return rtrim($uri, '/') ?: '/';
    }

    public function getHeader(string $name): ?string
    {
        $headers = function_exists('getallheaders') ? getallheaders() : $this->getAuthHeadersFallback();
        $name = strtolower($name);
        
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $name) {
                return $value;
            }
        }
        return null;
    }

    private function getAuthHeadersFallback(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }
}
