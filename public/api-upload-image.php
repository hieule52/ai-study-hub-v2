<?php
header('Content-Type: application/json');

// Kiểm tra thư mục lưu trữ
$uploadDir = __DIR__ . '/uploads/lessons/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'img_' . uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Trả về URL cho Quill
        echo json_encode([
            'url' => '/uploads/lessons/' . $filename
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Không thể lưu file']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}
