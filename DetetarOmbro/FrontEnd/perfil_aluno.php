<?php
session_start();
require_once 'db.php';
if (!isset($_GET['id'])) {
    die('Aluno não especificado.');
}
$id = intval($_GET['id']);
$db = getDB();
$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$aluno) { die('Aluno não encontrado.'); }
include 'templates/header.php';
?>
<div class="row mb-4">
    <div class="col-md-4 text-center">
        <img src="<?= htmlspecialchars($aluno['foto']) ?: 'https://via.placeholder.com/180x180?text=Aluno' ?>"
             alt="Foto do Aluno" class="img-thumbnail mb-3" style="max-width:180px;">
        <h3><?= htmlspecialchars($aluno['nome']) ?></h3>
        <p><b>Idade:</b> <?= htmlspecialchars($aluno['idade']) ?></p>
        <p><b>Contacto de emergência:</b><br> <?= htmlspecialchars($aluno['contacto_emergencia']) ?></p>
    </div>
    <div class="col-md-8">
        <h4>Observações</h4>
        <div class="mb-2"><?= nl2br(htmlspecialchars($aluno['observacoes'])) ?: '<span class="text-muted">Sem observações</span>' ?></div>
        <h4>Histórico de Lesões</h4>
        <div><?= nl2br(htmlspecialchars($aluno['historico_lesoes'])) ?: '<span class="text-muted">Sem histórico</span>' ?></div>
        <a class="btn btn-outline-primary mt-4" href="historico_aluno.php?id=<?= $aluno['id'] ?>">
            <i class="fa fa-history"></i> Ver histórico de alertas
        </a>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
