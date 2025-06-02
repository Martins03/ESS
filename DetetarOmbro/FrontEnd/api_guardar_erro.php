<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error_type = $_POST['error_type'] ?? '';
    $details = $_POST['details'] ?? '';
    $filename = $_POST['filename'] ?? null;

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO errors (timestamp, error_type, details, reviewed, filename) VALUES (NOW(), ?, ?, 0, ?)");
    $stmt->execute([$error_type, $details, $filename]);
    echo "OK";
}
