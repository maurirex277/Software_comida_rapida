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
    <title>Tomar Pedido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        .producto-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .pedido-lista {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📝 Tomar Pedido</h2>
        <a href="../dashboard.php" class="btn btn-secondary">🏠 Volver al menú</a>
    </div>

    <!-- Nombre del cliente -->
    <div class="mb-3">
        <label for="cliente" class="form-label">Nombre del Cliente</label>
        <input type="text" id="cliente" class="form-control" placeholder="Ingrese nombre del cliente">
    </div>

    <!-- Método de pago -->
    <div class="mb-3">
        <label for="metodo_pago" class="form-label">Método de Pago</label>
        <select id="metodo_pago" class="form-select">
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
        </select>
    </div>

    <!-- Buscador -->
    <input type="text" id="buscador" class="form-control mb-4" placeholder="Buscar producto...">

    <!-- Productos -->
    <div class="row" id="listaProductos">
        <?php
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
        while ($producto = $stmt->fetch()) {
            $stock_bajo = $producto['stock'] < 5 ? '<span class="badge bg-warning text-dark">⚠️ Bajo stock</span>' : '';
            echo "
            <div class='col-md-3 mb-4'>
                <div class='card producto-card shadow-sm'>
                    <img src='../productos/uploads/{$producto['imagen']}' class='card-img-top' alt='Producto'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$producto['nombre']}</h5>
                        <p class='card-text'>$ {$producto['precio']}</p>
                        $stock_bajo
                        <button class='btn btn-success btn-sm mt-2' onclick='agregarProducto(" . json_encode($producto) . ")'>➕ Agregar</button>
                    </div>
                </div>
            </div>";
        }
        ?>
    </div>

    <!-- Lista de pedido -->
    <h4 class="mt-4">🧾 Pedido</h4>
    <ul class="list-group mb-3 pedido-lista" id="pedidoLista"></ul>

    <!-- Botones -->
    <div class="d-flex gap-2">
        <button class="btn btn-primary" onclick="guardarPedido()">💾 Tomar Pedido</button>
        <button class="btn btn-danger" onclick="window.location.href='pagar_pedido.php'">💰 Pago Directo</button>
        <button class="btn btn-dark" onclick="imprimirTicket()">🖨️ Imprimir Ticket</button>
    </div>
</div>

<!-- Modal de ticket -->
<div class="modal fade" id="modalTicket" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" id="contenidoTicket">
      <div class="modal-header">
        <h5 class="modal-title">🧾 Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="ticketContenido">
        <!-- Se rellena por JS -->
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let pedido = [];

function agregarProducto(producto) {
    const existente = pedido.find(p => p.id === producto.id);
    if (existente) {
        existente.cantidad++;
    } else {
        pedido.push({...producto, cantidad: 1});
    }
    renderPedido();
}

function renderPedido() {
    const lista = document.getElementById('pedidoLista');
    lista.innerHTML = '';
    pedido.forEach((p, i) => {
        const item = document.createElement('li');
        item.className = 'list-group-item d-flex justify-content-between align-items-center';
        item.innerHTML = `
            ${p.nombre} x${p.cantidad} - $${(p.precio * p.cantidad).toFixed(2)}
            <div>
                <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${i})">🗑️</button>
            </div>
        `;
        lista.appendChild(item);
    });
}

function eliminarProducto(index) {
    pedido.splice(index, 1);
    renderPedido();
}

function guardarPedido() {
    const cliente = document.getElementById('cliente').value.trim();
    const metodoPago = document.getElementById('metodo_pago').value;

    if (!cliente) {
        alert('Por favor ingrese el nombre del cliente.');
        return;
    }
    if (pedido.length === 0) {
        alert('Agregue al menos un producto.');
        return;
    }

    const datos = {
        cliente: cliente,
        productos: pedido,
        metodo_pago: metodoPago
    };

    fetch('guardar_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Pedido guardado correctamente.');
            pedido = [];
            document.getElementById('cliente').value = '';
            renderPedido();
        } else if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Error desconocido.');
        }
    })
    .catch(() => alert('Error en la comunicación con el servidor.'));
}

function imprimirTicket() {
    const cliente = document.getElementById('cliente').value.trim();
    if (!cliente || pedido.length === 0) {
        alert('Complete los datos antes de imprimir el ticket.');
        return;
    }

    const datos = encodeURIComponent(JSON.stringify(pedido));
    const url = `imprimir_ticket.php?cliente=${encodeURIComponent(cliente)}&datos=${datos}`;
    
    window.open(url, '_blank');
}


// Buscador de productos
$('#buscador').on('keyup', function() {
    const valor = $(this).val().toLowerCase();
    $('#listaProductos .card').filter(function() {
        $(this).parent().toggle($(this).text().toLowerCase().indexOf(valor) > -1);
    });
});
</script>

</body>
</html>
