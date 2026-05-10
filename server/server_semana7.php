<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

$nombreServicio = "ServicioPagosAsincrono";
$ipServidor = "157.173.103.201"; 
$puertoServidor = 8080;

// 1. Registro en el Registry
$registry_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (@socket_connect($registry_socket, "127.0.0.1", 8081)) {
    $trama = "BIND|$nombreServicio|$ipServidor|$puertoServidor|EOF\n";
    socket_write($registry_socket, $trama, strlen($trama));
    socket_close($registry_socket);
}

// 2. Escucha del Servidor
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, "0.0.0.0", $puertoServidor);
socket_listen($socket, 5);

echo "=== SERVIDOR ASINCRONO EN ESCUCHA ===\n";

while (true) {
    $client_socket = socket_accept($socket);
    $payload = socket_read($client_socket, 1024);
    
    // Separamos la data: el objeto, la IP de ngrok y el puerto de ngrok
    $datos = explode('|', str_replace("|EOF\n", "", $payload));
    
    $objetoRecibido = unserialize($datos[0]);
    $ip_cliente = $datos[1];
    $puerto_callback = $datos[2];

    // 3. Respuesta Inmediata (Libera al cliente)
    $ack = "RECIBIDO_PROCESANDO|EOF\n";
    socket_write($client_socket, $ack, strlen($ack));
    socket_close($client_socket);

    echo "[+] Pago de $" . $objetoRecibido->monto . " recibido. Procesando...\n";
    
    // 4. Simulamos procesamiento bancario pesado
    sleep(3); 
    
    // 5. REMOTE CALLBACK (El Servidor llama al Cliente a través de Ngrok)
    echo "[!] Notificando resultado al cliente en $ip_cliente:$puerto_callback...\n";
    $callback_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (@socket_connect($callback_socket, $ip_cliente, $puerto_callback)) {
        $notificacion = "NOTIFICACION|Pago Aprobado exitosamente para " . $objetoRecibido->idAcudiente . "|EOF\n";
        socket_write($callback_socket, $notificacion, strlen($notificacion));
        socket_close($callback_socket);
        echo "    -> Notificación enviada.\n";
    } else {
        echo "    -> [ERROR] No se pudo contactar al cliente.\n";
    }
}