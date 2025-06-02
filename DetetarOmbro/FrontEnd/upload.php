<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['video'])) {
        http_response_code(400); echo "No video uploaded."; exit();
    }
    $error_type = $_POST['error_type'] ?? 'Desconhecido';
    $details = $_POST['details'] ?? '';
    $video_dir = __DIR__ . '/videos/';
    if (!is_dir($video_dir)) mkdir($video_dir, 0777, true);

    $filename = $video_dir . uniqid() . '.mp4';
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $filename)) {
        http_response_code(500); echo "Erro ao guardar vídeo."; exit();
    }

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO errors (error_type, details, filename) VALUES (?, ?, ?)");
    $stmt->execute([$error_type, $details, 'videos/' . basename($filename)]);
    echo "OK";
} else {
    http_response_code(405); echo "POST only.";
}
