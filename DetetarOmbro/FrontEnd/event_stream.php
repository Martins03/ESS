<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once 'db.php';
$db = getDB();

// Mantém a conexão aberta e envia erros atualizados
while (true) {
    $errors = $db->query("SELECT * FROM errors WHERE reviewed = 0 ORDER BY timestamp DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "data: " . json_encode($errors) . "\n\n";
    ob_flush();
    flush();
    sleep(3);
}
