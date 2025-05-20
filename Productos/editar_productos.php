<?php
require '../db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: productos.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}
?>

<!-- Modal Bootstrap abierto automáticamente -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Modal -->
<div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="actualizar_productos.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Editar Producto</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio:</label>
                <input type="number" class="form-control" name="precio" value="<?= $producto['precio'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock:</label>
                <input type="number" class="form-control" name="stock" value="<?= $producto['stock'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen actual:</label><br>
                <img src="uploads/<?= $producto['imagen'] ?>" alt="Imagen actual" width="100"><br><br>
                <input type="file" class="form-control" name="imagen">
                <input type="hidden" name="imagen_actual" value="<?= $producto['imagen'] ?>">
            </div>
        </div>
        <div class="modal-footer">
          <a href="productos.php" class="btn btn-secondary">Cancelar</a>
          <button type="submit" class="btn btn-primary" name="actualizar">Actualizar producto</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
