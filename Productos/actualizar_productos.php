<?php
require '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen_actual = $_POST['imagen_actual'];

    // Verificar si se subió una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = basename($_FILES['imagen']['name']);
        $ruta = "uploads/" . $imagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    } else {
        $imagen = $imagen_actual;
    }

    $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen, $id]);

    // Redirigimos con mensaje
    header("Location: productos.php?editado=1");
    exit;
}
?>
