<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

$nombreServicio = "ServicioPagosAsincrono";
$ipServidor = "157.173.103.201"; 
$puertoServidor = 8080;

// 1. Registro en el Registry (Puerto 8081)
$registry_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (@socket_connect($registry_socket, "127.0.0.1", 8081)) {
    $trama = "BIND|$nombreServicio|$ipServidor|$puertoServidor|EOF\n";
    socket_write($registry_socket, $trama, strlen($trama));
    socket_close($registry_socket);
}

// 2. Configuración de escucha
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, "0.0.0.0", $puertoServidor);
socket_listen($socket, 5);

echo "=== SERVIDOR ASINCRONO EN ESCUCHA (8080) ===\n";

while (true) {
    $client_socket = socket_accept($socket);
    $payload = socket_read($client_socket, 1024);
    
    // El payload del cliente semana 7 trae: OBJETO|IP_NGROK|PUERTO_NGROK|EOF
    $datos = explode('|', str_replace("|EOF\n", "", $payload));
    
    $objetoRecibido = unserialize($datos[0]);
    $ip_ngrok = $datos[1];
    $puerto_ngrok = (int)$datos[2];

    // 3. Respuesta Inmediata (Libera al cliente rápido)
    $ack = "RECIBIDO_PROCESANDO|EOF\n";
    socket_write($client_socket, $ack, strlen($ack));
    socket_close($client_socket);

    echo "[+] Pago de $" . $objetoRecibido->monto . " recibido. Procesando...\n";
    
    // 4. Simulamos proceso bancario
    sleep(3); 
    
    // 5. REMOTE CALLBACK: Llamamos al túnel de ngrok
    echo "[!] Notificando a ngrok en $ip_ngrok:$puerto_ngrok...\n";
    $callback_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (@socket_connect($callback_socket, $ip_ngrok, $puerto_ngrok)) {
        $notificacion = "NOTIFICACION|Pago Aprobado exitosamente para " . $objetoRecibido->idAcudiente . "|EOF\n";
        socket_write($callback_socket, $notificacion, strlen($notificacion));
        socket_close($callback_socket);
        echo "    -> Notificación enviada con éxito.\n";
    } else {
        echo "    -> [ERROR] No se pudo contactar con el túnel de ngrok.\n";
    }
}