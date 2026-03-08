<?php
// Configuración de red del Servidor (Team Master) - RED SÓLO ANFITRIÓN
$host = "192.168.56.101"; // IP Fija Estática
$port = 8080;             // Puerto exclusivo

echo "--- INICIANDO SERVIDOR TEAM MASTER ---\n";

// 1. Creación del Descriptor de Socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "ERROR TÉCNICO: Fallo al crear el socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}
echo "[OK] Socket creado exitosamente.\n";

// 2. Enlace (Bind)
$bind = socket_bind($socket, $host, $port);
if ($bind === false) {
    echo "ERROR TÉCNICO: Fallo en el Bind: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}
echo "[OK] Socket enlazado correctamente a $host:$port\n";

// Cierre temporal para la prueba de la Semana 3
socket_close($socket);
?>