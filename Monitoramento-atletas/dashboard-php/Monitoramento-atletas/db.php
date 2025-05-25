<?php
function getDB() {
    $db = new PDO('sqlite:' . __DIR__ . '/errors.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Create table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS errors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        error_type TEXT,
        details TEXT,
        filename TEXT,
        visualized INTEGER DEFAULT 0
    )");
    return $db;
}
?>
