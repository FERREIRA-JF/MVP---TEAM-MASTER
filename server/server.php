<?php
$host = "192.168.56.101"; // Tu IP
$port = 8080;

echo "--- INICIANDO SERVIDOR TEAM MASTER (SEMANA 3) ---\n";

// 1. Creación del Socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "[ERROR] Fallo al crear el socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}
echo "[OK] Socket creado exitosamente.\n";

// 2. Enlace (Bind) del Socket a la IP y Puerto
$bind = socket_bind($socket, $host, $port);
if ($bind === false) {
    echo "[ERROR] Fallo al enlazar el socket: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}
echo "[OK] Socket enlazado correctamente a $host:$port\n";

// Ponemos a escuchar solo UNA VEZ para el handshake de la Actividad 3
socket_listen($socket, 3);
echo "[ESCUCHANDO] Esperando el Handshake del cliente...\n";

// Aceptamos la conexión
$client_socket = socket_accept($socket);
if ($client_socket) {
    echo "[+] ¡Handshake recibido! Cliente conectado exitosamente.\n";
    socket_close($client_socket); // Cerramos el cliente al instante
}

// Cerramos el servidor (No hay ciclo while en la semana 3)
socket_close($socket);
echo "--- PRUEBA DE ENLACE FINALIZADA ---\n";
?>