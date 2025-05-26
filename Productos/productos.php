<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.html");
    exit;
}
require '../db_connect.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu propio CSS -->
    <link href="css/estilos_productos.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <!-- TOAST de éxito -->
    <?php if (isset($_GET['editado']) && $_GET['editado'] == 1): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="toastEditado" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ✅ Producto actualizado con éxito.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toastEditado');
                if (toast) toast.classList.remove('show');
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Lista de Productos</h2>
        <div>
            <a href="agregar_productos.php" class="btn btn-primary me-2">➕ Agregar Producto</a>
            <a href="../dashboard.php" class="btn btn-secondary">🏠 Volver al menú</a>
        </div>
    </div>

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" id="buscador" class="form-control shadow-sm" placeholder="🔍 Buscar producto por nombre o descripción...">
    </div>

    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
        while ($producto = $stmt->fetch()) {
            echo "<tr>
                    <td>{$producto['id']}</td>
                    <td>{$producto['nombre']}</td>
                    <td>{$producto['descripcion']}</td>
                    <td>$ {$producto['precio']}</td>
                    <td>{$producto['stock']}</td>
                    <td><img src='uploads/{$producto['imagen']}' alt='Imagen del producto' class='img-thumbnail' style='width: 100px; height: 100px;'></td>
                    <td>
                        <a href='editar_productos.php?id={$producto['id']}' class='btn btn-sm btn-warning'>✏️ Editar</a>
                        <button class='btn btn-sm btn-danger' data-id='{$producto['id']}' onclick='abrirModal(this)'>🗑️ Eliminar</button>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="tituloModal">¿Estás seguro?</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        Esta acción eliminará el producto permanentemente. ¿Deseas continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="mostrarToastCancelado()">Cancelar</button>
        <a id="btnConfirmarEliminar" href="#" class="btn btn-danger">Sí, eliminar</a>
      </div>
    </div>
  </div>
</div>

<!-- Toast de cancelación -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toastCancelado" class="toast text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        ❌ Eliminación cancelada.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  </div>
</div>

<!-- Bootstrap JS y lógica -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function abrirModal(boton) {
        const id = boton.getAttribute('data-id');
        document.getElementById('btnConfirmarEliminar').href = 'eliminar_productos.php?id=' + id;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
        modal.show();
    }

    function mostrarToastCancelado() {
        const toast = new bootstrap.Toast(document.getElementById('toastCancelado'));
        toast.show();
    }

    // Filtro en tiempo real
    document.getElementById('buscador').addEventListener('keyup', function () {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('table tbody tr');

        filas.forEach(fila => {
            const nombre = fila.children[1].textContent.toLowerCase();
            const descripcion = fila.children[2].textContent.toLowerCase();

            if (nombre.includes(filtro) || descripcion.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
