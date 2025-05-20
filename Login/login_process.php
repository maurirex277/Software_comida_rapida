<?php
session_start();
require '../db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['usuario'] = $user['username'];
    header("Location: ../dashboard.php");
    exit;
} else {
    // Mostrar error en pantalla para debug
    echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href = 'login.html';</script>";
    exit;
}
