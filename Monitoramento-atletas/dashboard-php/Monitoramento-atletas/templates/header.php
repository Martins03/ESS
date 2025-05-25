<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monitoramento de Atletas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Monitoramento de Atletas</a>
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
