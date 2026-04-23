<?php

// boot loader
$env = parse_ini_file(__DIR__ . '/.env');
foreach ($env as $key => $val) {
    $_ENV[$key] = $val;
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

try {
    $db = Database::connect();
    $stmt = $db->prepare("UPDATE courses SET status = 'approved' WHERE status = 'pending'");
    $stmt->execute();
    echo "Updated " . $stmt->rowCount() . " courses to approved.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
