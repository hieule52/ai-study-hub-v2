<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            $host = $_ENV["DB_HOST"];
            $dbname = $_ENV["DB_NAME"];
            $user = $_ENV["DB_USER"];
            $pass = $_ENV["DB_PASS"];

            try {
                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
