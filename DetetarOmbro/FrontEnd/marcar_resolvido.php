<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['aluno_nome'])) {
    $db = getDB();
    $id = intval($_POST['id']);
    $aluno_nome = trim($_POST['aluno_nome']);
    $stmt = $db->prepare("SELECT id FROM students WHERE nome = ?");
    $stmt->execute([$aluno_nome]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        $stmt = $db->prepare("UPDATE errors SET reviewed = 1, student_id = ? WHERE id = ?");
        $stmt->execute([$aluno['id'], $id]);
        header("Location: index.php?msg=ok");
        exit();
    } else {
        header("Location: index.php?msg=naoencontrado");
        exit();
    }
}
header("Location: index.php");
exit();
