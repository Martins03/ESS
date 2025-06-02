<?php
session_start();
require_once 'db.php';
if (!isset($_GET['id'])) { die('Aluno não especificado.'); }
$id = intval($_GET['id']);
$db = getDB();
$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$aluno) { die('Aluno não encontrado.'); }

$stmt = $db->prepare("SELECT * FROM errors WHERE student_id = ? ORDER BY timestamp DESC");
$stmt->execute([$id]);
$erros = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.php';
?>
<h3>
    <i class="fa fa-user"></i> Histórico de alertas: <?= htmlspecialchars($aluno['nome']) ?>
    <a href="perfil_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-outline-secondary btn-sm ms-2">Perfil</a>
</h3>
<div class="mb-4">
    <span class="text-muted">Idade: <?= htmlspecialchars($aluno['idade']) ?> &nbsp;|&nbsp;
    Contacto de emergência: <?= htmlspecialchars($aluno['contacto_emergencia']) ?></span>
</div>
<div style="max-height:420px;overflow:auto;">
    <table class="table table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Data/Hora</th>
                <th>Tipo de Erro</th>
                <th>Detalhes</th>
                <th>Feedback PT</th>
                <th>Status</th>
                <th>Vídeo</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($erros as $e): ?>
        <tr class="<?= $e['reviewed'] ? 'table-success' : 'table-warning' ?>">
            <td><?=htmlspecialchars($e['timestamp'])?></td>
            <td><?=htmlspecialchars($e['error_type'])?></td>
            <td><?=htmlspecialchars($e['details'])?></td>
            <td><?=htmlspecialchars($e['feedback'] ?? '')?></td>
            <td>
                <?= $e['reviewed']
                    ? "<span class='badge bg-success'><i class='fa fa-check'></i> Corrigido</span>"
                    : "<span class='badge bg-warning text-dark'><i class='fa fa-exclamation-triangle'></i> Novo</span>" ?>
            </td>
            <td>
                <?php if($e['filename']): ?>
                <a href="<?=htmlspecialchars($e['filename'])?>" target="_blank"><i class="fa fa-play"></i> Ver vídeo</a>
                <?php else: ?>
                    <span class="text-muted">Sem vídeo</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'templates/footer.php'; ?>
