<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.html");
    exit;
}

require '../db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Primero, buscamos la imagen para eliminarla del servidor si existe
    $stmtImg = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmtImg->execute([$id]);
    $producto = $stmtImg->fetch();

    if ($producto && !empty($producto['imagen'])) {
        $rutaImagen = 'uploads/' . $producto['imagen'];
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen); // Elimina la imagen del servidor
        }
    }

    // Luego, eliminamos el producto de la base de datos
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    // Redirigimos con mensaje de éxito
    header("Location: productos.php?eliminado=1");
    exit;
} else {
    // Si no se pasó ID, volvemos sin hacer nada
    header("Location: productos.php");
    exit;
}
