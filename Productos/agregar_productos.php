<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.html");
    exit;
}

require '../db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $imagenNombre = null;

    if (!$nombre || !$precio || !$stock) {
        $error = "Los campos Nombre, Precio y Stock son obligatorios.";
    } elseif (!is_numeric($precio) || $precio <= 0) {
        $error = "El precio debe ser un número positivo.";
    } elseif (!is_numeric($stock) || $stock < 0) {
        $error = "El stock debe ser un número cero o positivo.";
    } else {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['imagen']['tmp_name'];
            $imagenNombre = basename($_FILES['imagen']['name']);
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadFile = $uploadDir . $imagenNombre;
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExt = strtolower(pathinfo($imagenNombre, PATHINFO_EXTENSION));
            if (!in_array($fileExt, $allowedExt)) {
                $error = "Solo se permiten imágenes JPG, JPEG, PNG y GIF.";
            } elseif (!move_uploaded_file($tmpName, $uploadFile)) {
                $error = "Error al subir la imagen.";
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $imagenNombre])) {
                $success = "¡Producto agregado con éxito!";
                // Limpiar los campos
                $nombre = $descripcion = $precio = $stock = '';
            } else {
                $error = "Error al guardar el producto en la base de datos.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Agregar Producto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .ventana-formulario {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .ventana-formulario.mostrar {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="ventana-formulario" id="formularioVentana">
        <h3 class="mb-4">Agregar Nuevo Producto</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion"><?= htmlspecialchars($descripcion ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio *</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?= htmlspecialchars($precio ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock *</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($stock ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del producto</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Agregar Producto</button>
                <a href="productos.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
<script>
    function mostrarAnimacionFormulario() {
        const form = document.getElementById("formularioVentana");
        form.classList.remove("mostrar"); // Quita animación si estaba
        void form.offsetWidth; // Fuerza reflujo (reinicio de animación)
        form.classList.add("mostrar"); // Vuelve a aplicar animación
    }

    window.addEventListener("pageshow", () => {
        mostrarAnimacionFormulario();

        // Ocultar automáticamente la alerta de éxito luego de 5 segundos
        const alertaExito = document.querySelector('.alert-success');
        if (alertaExito) {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertaExito);
                bsAlert.close();
            }, 5000);
        }
    });
</script>
</body>
</html>
