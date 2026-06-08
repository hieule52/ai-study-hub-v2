<?php
require 'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/vendor/autoload.php';
use App\Core\Database;
use App\Core\Env;
use App\Services\VideoService;

if (file_exists('g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/.env')) {
    Env::load('g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/.env');
}

$realVideo = 'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/5/5/a5b431750fc234bdf48d880450597140.mp4';
if (!file_exists($realVideo)) {
    die("Real video file not found at $realVideo\n");
}

// Create a dummy video file of 2KB from the real video header
$dummyFile = __DIR__ . '/dummy_video.mp4';
$content = file_get_contents($realVideo, false, null, 0, 2048);
file_put_contents($dummyFile, $content);

$_FILES['video'] = [
    'name' => 'dummy_video.mp4',
    'type' => 'video/mp4',
    'tmp_name' => $dummyFile,
    'error' => UPLOAD_ERR_OK,
    'size' => 2048
];

try {
    $videoService = new VideoService();
    // Use course ID 12, chapter ID 14
    $result = $videoService->uploadVideo($_FILES['video'], 12, 14);
    echo "SUCCESS: " . print_r($result, true) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    if (file_exists($dummyFile)) {
        unlink($dummyFile);
    }
}
