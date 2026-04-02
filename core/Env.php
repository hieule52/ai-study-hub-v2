<?php

namespace App\Core;

class Env
{
    public static function load($path)
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            // Bỏ qua comment
            if (str_starts_with(trim($line), '#')) continue;

            $parts = explode('=', $line, 2);

            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}
