<?php
require_once 'db.php';

$db = getDB();
$errors = $db->query("SELECT * FROM errors WHERE reviewed = 0 ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($errors);
