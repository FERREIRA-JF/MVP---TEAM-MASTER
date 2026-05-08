<?php
// registry.php - El Directorio Telefónico del Sistema Distribuido
$host = "0.0.0.0"; 
$port = 8081; // Usaremos un puerto diferente para el Registry

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket, 5);

// Tabla de búsqueda en memoria
$directorio = [];

echo "=== REGISTRY TEAM MASTER ACTIVO (PUERTO 8081) ===\n";

while (true) {
    $client_socket = socket_accept($socket);
    $payload = socket_read($client_socket, 1024);
    
    // Trama esperada: ACCION|NOMBRE|IP|PUERTO|EOF
    $partes = explode('|', str_replace("|EOF\n", "", $payload));
    $accion = $partes[0];
    $nombre = $partes[1];

    if ($accion == "BIND") {
        $ip = $partes[2];
        $puerto = $partes[3];
        $directorio[$nombre] = ['ip' => $ip, 'puerto' => $puerto];
        echo "[REGISTRO] Servicio '$nombre' vinculado a $ip:$puerto\n";
        $respuesta = "REGISTRO_OK|EOF\n";
    } 
    elseif ($accion == "LOOKUP") {
        if (isset($directorio[$nombre])) {
            $respuesta = $directorio[$nombre]['ip'] . "|" . $directorio[$nombre]['puerto'] . "|EOF\n";
            echo "[BUSQUEDA] Cliente consultó por '$nombre'. Enviando dirección.\n";
        } else {
            $respuesta = "NOT_FOUND|EOF\n";
        }
    }

    socket_write($client_socket, $respuesta, strlen($respuesta));
    socket_close($client_socket);
}