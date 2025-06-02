<?php
session_start();
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$db = getDB();

if ($id) {
    $stmt = $db->prepare("SELECT * FROM students WHERE id=?");
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$aluno) die('Aluno não encontrado.');
} else {
    $aluno = ['nome'=>'', 'idade'=>'', 'foto'=>'', 'contacto_emergencia'=>'', 'historico_lesoes'=>'', 'observacoes'=>''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $contacto = $_POST['contacto_emergencia'] ?? '';
    $lesoes = $_POST['historico_lesoes'] ?? '';
    $obs = $_POST['observacoes'] ?? '';
    $foto = $aluno['foto'];

    // Upload de foto (opcional)
    if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name']) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'uploads/aluno_' . uniqid() . '.' . $ext;
        if (!is_dir('uploads')) mkdir('uploads');
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    if ($id) {
        $stmt = $db->prepare("UPDATE students SET nome=?, idade=?, foto=?, contacto_emergencia=?, historico_lesoes=?, observacoes=? WHERE id=?");
        $stmt->execute([$nome, $idade, $foto, $contacto, $lesoes, $obs, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO students (nome, idade, foto, contacto_emergencia, historico_lesoes, observacoes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $idade, $foto, $contacto, $lesoes, $obs]);
    }
    header("Location: alunos.php");
    exit();
}

include 'templates/header.php';
?>
<h2><?= $id ? "Editar Aluno" : "Adicionar Aluno" ?></h2>
<form method="post" enctype="multipart/form-data" class="mb-5" style="max-width:500px;">
    <div class="mb-2">
        <label class="form-label">Nome</label>
        <input name="nome" class="form-control" value="<?= htmlspecialchars($aluno['nome']) ?>" required>
    </div>
    <div class="mb-2">
        <label class="form-label">Idade</label>
        <input name="idade" type="number" class="form-control" value="<?= htmlspecialchars($aluno['idade']) ?>">
    </div>
    <div class="mb-2">
        <label class="form-label">Foto</label>
        <?php if ($aluno['foto']): ?>
            <img src="<?= htmlspecialchars($aluno['foto']) ?>" style="height:48px;border-radius:10px;" class="mb-2"><br>
        <?php endif; ?>
        <input type="file" name="foto" class="form-control">
    </div>
    <div class="mb-2">
        <label class="form-label">Contacto de Emergência</label>
        <input name="contacto_emergencia" class="form-control" value="<?= htmlspecialchars($aluno['contacto_emergencia']) ?>">
    </div>
    <div class="mb-2">
        <label class="form-label">Histórico de Lesões</label>
        <textarea name="historico_lesoes" class="form-control" rows="2"><?= htmlspecialchars($aluno['historico_lesoes']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Observações</label>
        <textarea name="observacoes" class="form-control" rows="2"><?= htmlspecialchars($aluno['observacoes']) ?></textarea>
    </div>
    <button class="btn btn-primary"><?= $id ? "Guardar Alterações" : "Adicionar Aluno" ?></button>
    <a href="alunos.php" class="btn btn-secondary ms-2">Cancelar</a>
</form>
<?php include 'templates/footer.php'; ?>
