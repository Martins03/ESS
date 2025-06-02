<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Detetar Ombro - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="logo.png">
</head>
<body style="background:#f7f7fa;">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">Detetar Ombro</a>
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
            <span class="text-white me-3"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? '') ?></span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
