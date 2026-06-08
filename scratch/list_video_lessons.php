<?php
require 'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/vendor/autoload.php';
use App\Core\Database;
use App\Core\Env;

if (file_exists('g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/.env')) {
    Env::load('g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/.env');
}

try {
    $db = Database::connect();
    $stmt = $db->query("SELECT id, title, content_type, video_filename, video_url FROM lessons WHERE content_type = 'video'");
    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total video lessons: " . count($lessons) . "\n";
    foreach ($lessons as $l) {
        echo "ID: {$l['id']} | Title: {$l['title']} | Filename: '{$l['video_filename']}' | URL: '{$l['video_url']}'\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
