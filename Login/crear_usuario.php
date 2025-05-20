<?php
require '../db_connect.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$tipo = 'admin';

$stmt = $pdo->prepare("INSERT INTO usuarios (username, password, tipo) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $tipo]);

echo "Usuario admin creado";