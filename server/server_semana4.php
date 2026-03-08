<?php
$host = "192.168.56.101";
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);

// Actividad 1: Algoritmos de Escucha (Listen)
socket_listen($socket, 5);
echo "=== SERVIDOR TEAM MASTER ACTIVO ===\n";
echo "Esperando transacciones en $host:$port...\n";

// Actividad 1: El ciclo while(true) mantiene el servidor persistente
while (true) {
    // Actividad 1: Accept
    $client_socket = socket_accept($socket);
    echo "\n[+] Nueva conexion entrante aceptada.\n";

    // Actividad 2: Intercambio de Payload (Lectura)
    // Leemos hasta 1024 bytes.
    $payload = socket_read($client_socket, 1024);
    echo "[RECIBIDO] Trama del cliente: " . trim($payload) . "\n";

    // Actividad 2: Procesamiento y Escritura (con delimitador EOF)
    $respuesta = "TRANSACCION_EXITOSA|NUEVO_ESTADO:VERDE|EOF\n";
    socket_write($client_socket, $respuesta, strlen($respuesta));
    echo "[ENVIADO] Recibo enviado al cliente.\n";

    // Actividad 3: Protocolo de Cierre (Liberar recursos del cliente)
    socket_close($client_socket);
    echo "[-] Conexion con el cliente cerrada de forma segura.\n";
}

// Actividad 3: Supresión del socket principal (Buena práctica)
socket_close($socket);
?>