<?php
$files = [
    'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/8/9/d0ddcff262db901c2d2ce32963c2e8d7.mp4',
    'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/5/5/a5b431750fc234bdf48d880450597140.mp4',
    'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/6/6/1b00646e5519e8410d022758ab1d07aa.mp4',
    'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/7/8/19babf386837836e813d5a6bb435dd55.mp4',
    'g:/My Drive/DuAnCaNhan/AI Study Hub LMS/AIStudyHubLMS/storage/private/videos/8/1/e5959c448516dfbd40af75d0f2968b83.mp4'
];

foreach ($files as $f) {
    echo "$f : " . (file_exists($f) ? "EXISTS" : "NOT FOUND") . "\n";
}
