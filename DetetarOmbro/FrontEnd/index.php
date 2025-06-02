<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit(); }
require_once 'db.php';
include 'templates/header.php';
?>

<!-- Menu de navegação -->
<nav class="mb-4">
    <ul class="nav nav-pills">
        <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fa fa-home"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="alunos.php"><i class="fa fa-users"></i> Alunos</a></li>
        <li class="nav-item"><a class="nav-link" href="aluno_form.php"><i class="fa fa-user-plus"></i> Adicionar Aluno</a></li>
        <li class="nav-item ms-auto">
            <span class="nav-link disabled"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']) ?></span>
        </li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<!-- Banner -->
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="fa fa-info-circle fa-lg me-2"></i>
    <div>
        <b>Bem-vindo, <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username']) ?>!</b><br>
        Monitoriza aqui os erros de execução, consulta os vídeos e marca como corrigidos.
    </div>
</div>

<!-- Câmara via iframe -->
<div class="card shadow-sm mb-4" style="max-width: 1100px; margin: 0 auto;">
    <div class="card-header bg-dark text-white">
        <i class="fa fa-video"></i> Câmara em tempo real (Python/Flask)
    </div>
    <div class="card-body text-center" style="background:#eaeaea; min-height:600px;">
        <iframe src="camera.php"
                id="iframe-camera"
                style="width: 100%; max-width: 960px; aspect-ratio: 16/9; border-radius:16px; border: 4px solid #255C99; box-shadow: 0 6px 28px 0 #23272b22;"
                frameborder="0"
                allowfullscreen>
        </iframe>
    </div>
</div>

<!-- Tabela -->
<h5 class="mb-3"><i class="fa fa-exclamation-triangle text-warning"></i> Alertas Detetados</h5>
<div style="max-height:360px;overflow:auto;">
    <table class="table table-striped align-middle small">
        <thead class="table-dark">
            <tr>
                <th>Data/Hora</th>
                <th>Erro</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tabela-erros">
            <!-- SSE irá preencher -->
        </tbody>
    </table>
</div>

<?php include 'templates/footer.php'; ?>

<script>
const source = new EventSource("event_stream.php");

source.onmessage = function(event) {
    const tbody = document.getElementById("tabela-erros");
    if (!tbody) return;

    const data = JSON.parse(event.data);
    let novo = "";

    data.forEach(erro => {
        novo += `
            <tr class="table-warning">
                <td>${erro.timestamp}</td>
                <td>
                    <b>${erro.error_type}</b><br>
                    <span class="text-muted">${erro.details}</span>
                    ${erro.filename ? `<br><a href="${erro.filename}" target="_blank" class="link-primary"><i class="fa fa-play"></i> Vídeo</a>` : ""}
                </td>
                <td><span class='badge bg-warning text-dark'><i class='fa fa-exclamation-triangle'></i> Novo</span></td>
                <td>
                    <form method="post" action="marcar_resolvido.php" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="id" value="${erro.id}">
                        <input type="text" name="aluno_nome" class="form-control form-control-sm" placeholder="Nome do aluno" required>
                        <button class="btn btn-sm btn-success" type="submit">
                            <i class="fa fa-check"></i> Resolvido
                        </button>
                    </form>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = novo;
};
</script>
