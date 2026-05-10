<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

echo "--- SISTEMA ACUDIENTES - CLIENTE ASINCRONO ---\n";

$miPago = new PagoDTO("ACU_002", 85000, "DOTACION_DEPORTIVA");
$ip_registry = "157.173.103.201"; 

// 1. LOOKUP para encontrar el servidor
$r_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
@socket_connect($r_socket, $ip_registry, 8081);
socket_write($r_socket, "LOOKUP|ServicioPagosAsincrono|EOF\n");
$resp_lookup = trim(socket_read($r_socket, 1024));
socket_close($r_socket);

list($ip_real, $puerto_real) = explode('|', str_replace("|EOF", "", $resp_lookup));

// 2. DATOS DE TU NGROK ACTUAL
$ngrok_host = "8.tcp.ngrok.io"; 
$ngrok_port = 11766; 
$ip_ngrok = gethostbyname($ngrok_host); // Traduce a IP numérica

// 3. ENVIAR PAGO
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, $ip_real, $puerto_real);

$payload = serialize($miPago) . "|$ip_ngrok|$ngrok_port|EOF\n";
socket_write($socket, $payload, strlen($payload));

$ack = socket_read($socket, 1024);
socket_close($socket);

echo "[1] Respuesta del Servidor: " . trim($ack) . "\n";
echo "[2] Abriendo puerto 8082 local para recibir el Callback de ngrok...\n";

// 4. EL CLIENTE ESPERA LA NOTIFICACIÓN (Callback)
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($listen_socket, "0.0.0.0", 8082); 
socket_listen($listen_socket, 1);

$server_conn = socket_accept($listen_socket);
if ($server_conn) {
    $notificacion = socket_read($server_conn, 1024);
    echo "\n[!!!] NOTIFICACIÓN RECIBIDA DESDE EL VPS: \n" . trim($notificacion) . "\n";
    socket_close($server_conn);
}
socket_close($listen_socket);