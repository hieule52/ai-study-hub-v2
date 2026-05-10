<?php

namespace App\Core;

class Request
{
    private array $data      = []; // merged GET + Body
    private array $queryData = []; // GET-only params
    private array $bodyData  = []; // POST/PUT Body-only params

    public mixed $user = null; // Populated by AuthMiddleware

    public function __construct()
    {
        $this->parse();
    }

    private function parse(): void
    {
        // Parse GET query string params
        $this->queryData = $_GET;

        // Parse POST/PUT JSON payload or Form data
        $method = $_SERVER['REQUEST_METHOD'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if (strpos($contentType, 'application/json') !== false) {
                // JSON Data
                $input = file_get_contents('php://input');
                $json = json_decode($input, true);
                if (is_array($json)) {
                    $this->bodyData = $json;
                }
            } else {
                // Form data
                $this->bodyData = $_POST;
            }
        }

        // Merged: body takes priority over query for same keys
        $this->data = array_merge($this->queryData, $this->bodyData);
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

    /**
     * Read from merged data (GET + Body)
     */
    public function input(string $key, $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Read from GET query string ONLY — use for pagination, filters, search
     */
    public function query(string $key, $default = null): mixed
    {
        return $this->queryData[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->bodyData; // all() returns BODY data only (security: no GET params leaking into body handlers)
    }
}

