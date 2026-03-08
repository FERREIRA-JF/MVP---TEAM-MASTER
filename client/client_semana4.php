<?php
$host = "192.168.56.101";
$port = 8080;

echo "--- SISTEMA ACUDIENTES - TEAM MASTER ---\n";
echo "Conectando al servidor...\n";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$conexion = @socket_connect($socket, $host, $port);

if ($conexion) {
    echo "[OK] Conectado al servidor.\n";

    // Actividad 2: Creación del Payload con delimitadores (| y EOF)
    $mensaje = "ID_ACUDIENTE:001|MONTO:50000|TIPO:MENSUALIDAD|EOF\n";
    
    // Actividad 2: Escritura (Enviar al servidor)
    socket_write($socket, $mensaje, strlen($mensaje));
    echo "[ENVIADO] Trama de pago enviada.\n";

    // Actividad 2: Lectura de la respuesta del servidor
    $respuesta = socket_read($socket, 1024);
    echo "[RESPUESTA DEL SERVIDOR] " . trim($respuesta) . "\n";

} else {
    echo "[ERROR] No se pudo establecer la conexión.\n";
}

// Actividad 3: Protocolo de Cierre
socket_close($socket);
echo "[-] Socket del cliente cerrado.\n";
?>