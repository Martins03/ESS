<?php
session_start();
require_once 'db.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    header("Location: index.php");
    exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && $u['password'] === $pass) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $u['username'];
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['name'] = $u['name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Credenciais inválidas!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento de Atletas com Visão Computacional - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="css/logo.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-card mx-auto">
        <div class="text-center mb-4">
            <i class="fa fa-dumbbell"></i>
            <h3 class="login-title">Monitoramento de Atletas com Visão Computacional </h3>
            <div class="small text-secondary mb-2">Gym Dashboard Login</div>
        </div>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label login-label">Utilizador</label>
                <input class="form-control shadow-inset" type="text" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label login-label">Senha</label>
                <input class="form-control shadow-inset" type="password" name="password" required>
            </div>
            <button class="btn w-100 py-2 mt-2" type="submit">
                <i class="fa fa-sign-in-alt"></i> Entrar
            </button>
            <?php if($error): ?>
                <div class="alert alert-danger mt-3 mb-0 py-2 px-3"><?= $error ?></div>
            <?php endif; ?>
        </form>
        <div class="mt-3 text-center">
            <small class="text-muted">© <?=date('Y')?> Monitoramento de Atletas com Visão Computacional System</small>
        </div>
    </div>
</body>
</html>
