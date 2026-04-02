<?php

namespace App\Core;
// Base Controller class to handle view rendering
class Controller
{
    public function view($path, $data = [])
    {
        extract($data);

        $file = __DIR__ . '/../app/Views/' . $path . '.php';

        if (!file_exists($file)) {
            die("View not found: $file");
        }

        require $file;
    }
}
