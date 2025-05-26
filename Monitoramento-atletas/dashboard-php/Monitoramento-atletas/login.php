<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    header("Location: index.php"); exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if ($user === 'admin' && $pass === '1234') { // Change for production!
        $_SESSION['loggedin'] = true;
        header("Location: index.php"); exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<?php include 'templates/header.php'; ?>
<h2>Login</h2>
<form method="post" class="w-25 mx-auto">
    <input class="form-control mb-2" type="text" name="username" placeholder="User" required>
    <input class="form-control mb-2" type="password" name="password" placeholder="Pass" required>
    <button class="btn btn-primary w-100" type="submit">Login</button>
    <?php if($error) echo "<div class='text-danger mt-2'>$error</div>"; ?>
</form>
<?php include 'templates/footer.php'; ?>
