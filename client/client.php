<?php
$host = "192.168.56.101"; // IP del servidor Team Master
$port = 8080;

echo "--- SISTEMA ACUDIENTES - TEAM MASTER ---\n";
echo "Conectando al servidor...\n";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$conexion = socket_connect($socket, $host, $port);

if ($conexion) {
    echo "[OK] Conectado al servidor.\n";
    
    // Trama de pago a enviar (El Payload)
    $mensaje = "PAGAR|ACUDIENTE_001|50000|TRANSFERENCIA|EOF";
    
    // Enviamos el pago
    socket_write($socket, $mensaje, strlen($mensaje));
    echo "[>>] Enviando pago: $mensaje\n";
    
    // Recibimos el recibo/respuesta del servidor
    $respuesta = socket_read($socket, 1024);
    echo "[<<] Respuesta del servidor: " . trim($respuesta) . "\n";
    
    // Cierre seguro
    socket_close($socket);
    echo "Transaccion finalizada.\n";
} else {
    echo "Error de conexion.\n";
}
?>