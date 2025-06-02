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
    <title>Detetar Ombro - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.6)), 
            url('css/bg-gym.jpg') center center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255,255,255,0.92);
            border-radius: 18px;
            box-shadow: 0 0 24px 2px rgba(0,0,0,0.19);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 340px;
            width: 100%;
        }
        .login-card .fa-dumbbell {
            color: #255C99;
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-card .btn {
            background: #255C99;
            color: #fff;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .login-card .btn:hover {
            background: #18345c;
        }
        .login-title {
            color: #255C99;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .login-label {
            color: #333;
        }
        .shadow-inset {
            box-shadow: inset 0 1px 4px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="login-card mx-auto">
        <div class="text-center mb-4">
            <i class="fa fa-dumbbell"></i>
            <h3 class="login-title">Detetar Ombro</h3>
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
            <small class="text-muted">© <?=date('Y')?> Detetar Ombro Gym System</small>
        </div>
    </div>
</body>
</html>
