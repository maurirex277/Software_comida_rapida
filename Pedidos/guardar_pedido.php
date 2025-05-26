<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require '../db_connect.php';

// Recibir los datos JSON enviados desde tomar_pedido.php
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    echo json_encode(['error' => 'Datos no recibidos']);
    exit;
}

$cliente = $datos['cliente'] ?? '';
$productos = $datos['productos'] ?? [];
$metodo_pago = $datos['metodo_pago'] ?? 'efectivo';

if (empty($cliente) || empty($productos)) {
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_nombre, metodo_pago) VALUES (:cliente, :metodo_pago)");
    $stmt->execute([
        ':cliente' => $cliente,
        ':metodo_pago' => $metodo_pago
    ]);

    // Obtener el ID del pedido insertado
    $pedido_id = $pdo->lastInsertId();

    // Insertar detalle del pedido
    $sqlDetalle = "INSERT INTO pedido_detalle (pedido_id, producto_id, cantidad) VALUES (:pedido_id, :producto_id, :cantidad)";
    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $producto) {
        $stmtDetalle->execute([
            ':pedido_id' => $pedido_id,
            ':producto_id' => $producto['id'],
            ':cantidad' => $producto['cantidad']
        ]);
    }

    // Confirmar transacción
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Error al guardar el pedido: ' . $e->getMessage()]);
}
