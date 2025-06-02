<?php
session_start();
require_once 'db.php';
$db = getDB();
$alunos = $db->query("SELECT * FROM students ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
include 'templates/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Lista de Alunos</h2>
    <a href="aluno_form.php" class="btn btn-success"><i class="fa fa-plus"></i> Adicionar Aluno</a>
</div>
<ul class="list-group mb-5">
<?php foreach($alunos as $a): ?>
  <li class="list-group-item d-flex align-items-center">
    <img src="<?= htmlspecialchars($a['foto']) ?: 'https://via.placeholder.com/32x32?text=A' ?>"
         style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:12px;">
    <b><?= htmlspecialchars($a['nome']) ?></b> (<?= $a['idade'] ?> anos)
    <a class="btn btn-outline-primary btn-sm ms-auto me-2" href="perfil_aluno.php?id=<?= $a['id'] ?>">Perfil</a>
    <a class="btn btn-outline-secondary btn-sm" href="aluno_form.php?id=<?= $a['id'] ?>"><i class="fa fa-edit"></i> Editar</a>
  </li>
<?php endforeach; ?>
</ul>
<?php include 'templates/footer.php'; ?>
