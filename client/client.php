<?php
$host = "192.168.56.101";
$port = 8080;

echo "--- SIMULACIÓN DE HANDSHAKE TEAM MASTER ---\n";
echo "Intentando conectar a $host:$port...\n";

// Creamos el socket del cliente
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// Intentamos la conexión (Handshake)
$conexion = @socket_connect($socket, $host, $port);

if ($conexion) {
    echo "[OK] Handshake exitoso. ¡El servidor reconocio el enlace!\n";
} else {
    echo "[ERROR] No se pudo conectar al servidor.\n";
}

// Cerramos la conexión
socket_close($socket);
?>