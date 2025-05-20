<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: Login/login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Software Comida Rápida</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="bg-light">

    <!-- Top Bar -->
    <div class="top-bar">
        <div><strong>Bienvenido, <?php echo $_SESSION['usuario']; ?> 👋</strong></div>
        <a href="Login/logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
    </div>

    <!-- Contenido del dashboard -->
    <div class="container container-custom mt-5">
        <div class="row g-4">
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Productos/productos.php" class="btn btn-primary w-100 dashboard-btn">📦 Productos</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Empleados/empleados.php" class="btn btn-success w-100 dashboard-btn">👨‍🍳 Empleados</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Pedidos/tomar_pedido.php" class="btn btn-warning w-100 dashboard-btn">🛒 Tomar Pedido</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Pedidos/pagar_pedido.php" class="btn btn-info w-100 dashboard-btn">💵 Pagar Pedido</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Caja/cierre_caja.php" class="btn btn-dark w-100 dashboard-btn">💰 Cierre de Caja</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="Registro_admin/registrar_admin.php" class="btn btn-light w-100 dashboard-btn">🧑‍💼 Registrar Admin</a>
            </div>
        </div>
    </div>

</body>
</html>
