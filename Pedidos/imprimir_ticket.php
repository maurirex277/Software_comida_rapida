<?php
require('../FPDF/fpdf.php'); 

if (!isset($_GET['cliente']) || !isset($_GET['datos'])) {
    echo "Datos incompletos.";
    exit;
}

$cliente = utf8_decode(htmlspecialchars($_GET['cliente']));
$productos = json_decode($_GET['datos'], true);

if (!$productos) {
    echo "Error al procesar productos.";
    exit;
}

// Ticket de 80mm ancho
$pdf = new FPDF('P', 'mm', array(80, 150));
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5); // margen izquierdo, superior, derecho
$pdf->SetFont('Arial', 'B', 12);
$titulo = utf8_decode('COMIDA RÁPIDA');
$pdf->SetFont('Arial', 'B', 12);
$anchoTexto = $pdf->GetStringWidth($titulo);
$anchoPagina = 80 - 10; // ancho del ticket (80mm) menos márgenes (5 izquierda y 5 derecha)
$x = (80 - $anchoTexto) / 2;
$pdf->SetX($x);
$pdf->Cell($anchoTexto, 6, $titulo, 0, 1);
$pdf->Ln(2);

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Cliente y fecha
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Cliente: ' . $cliente, 0, 1, 'C');
$pdf->Cell(0, 5, 'Fecha: ' . date("d/m/Y H:i"), 0, 1, 'C');
$pdf->Ln(2);

// Línea
$pdf->Cell(0, 0, str_repeat('-', 36), 0, 1, 'C');
$pdf->Ln(2);

// Encabezado de la tabla
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(38, 5, 'Producto', 0, 0, 'L');
$pdf->Cell(12, 5, 'Cant.', 0, 0, 'C');
$pdf->Cell(20, 5, 'Precio', 0, 1, 'R');

// Productos
$pdf->SetFont('Arial', '', 9);
$total = 0;
foreach ($productos as $p) {
    $nombre = utf8_decode(substr($p['nombre'], 0, 20));
    $cantidad = $p['cantidad'];
    $precio = number_format($p['precio'], 2);
    $subtotal = number_format($p['precio'] * $cantidad, 2);
    
    $pdf->Cell(38, 5, $nombre, 0, 0, 'L');
    $pdf->Cell(12, 5, $cantidad, 0, 0, 'C');
    $pdf->Cell(20, 5, '$' . $precio, 0, 1, 'R');

    $total += $p['precio'] * $p['cantidad'];
}

$pdf->Ln(2);
$pdf->Cell(0, 0, str_repeat('-', 36), 0, 1, 'C');
$pdf->Ln(2);

// Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 6, 'TOTAL', 0, 0, 'L');
$pdf->Cell(0, 6, '$' . number_format($total, 2), 0, 1, 'R');

$pdf->Ln(4);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, utf8_decode('¡Gracias por su compra!'), 0, 1, 'C');

$pdf->Output('I', 'ticket.pdf');
