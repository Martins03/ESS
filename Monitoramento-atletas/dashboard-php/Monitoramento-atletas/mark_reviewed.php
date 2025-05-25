<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php"); exit();
}
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE errors SET visualized = 1 WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}
header("Location: index.php");
exit();
