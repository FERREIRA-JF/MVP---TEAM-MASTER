<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

echo "--- SISTEMA ACUDIENTES - MODO ASINCRONO CON NGROK ---\n";

$miPago = new PagoDTO("ACU_002", 85000, "DOTACION_DEPORTIVA");

$ip_registry = "157.173.103.201"; 

// 1. LOOKUP: Preguntamos al registry la IP del servidor
$r_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
@socket_connect($r_socket, $ip_registry, 8081);
socket_write($r_socket, "LOOKUP|ServicioPagosAsincrono|EOF\n");
$resp_lookup = trim(socket_read($r_socket, 1024));
socket_close($r_socket);

list($ip_real, $puerto_real) = explode('|', str_replace("|EOF", "", $resp_lookup));

// --- MODIFICACIÓN NGROK ---
// Escribe aquí lo que te da la terminal de ngrok (cambia estos valores por los tuyos)
$ngrok_host = "0.tcp.sa.ngrok.io"; 
$ngrok_port = 14567;               

// Traducimos el dominio de ngrok a IP numérica para el servidor de Ubuntu
$ip_publica_ngrok = gethostbyname($ngrok_host);
// --------------------------

// 2. ENVIAR EL PAGO
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, $ip_real, $puerto_real);

// Le decimos al servidor: "Este es el pago. Llámame a ngrok cuando termines"
$payload = serialize($miPago) . "|$ip_publica_ngrok|$ngrok_port|EOF\n";
socket_write($socket, $payload, strlen($payload));

// Recibimos el "RECIBIDO_PROCESANDO"
$ack = socket_read($socket, 1024);
socket_close($socket);

echo "[1] Respuesta del Servidor: " . trim($ack) . "\n";
echo "[2] Abriendo puerto local 8082 para esperar notificacion a traves de ngrok...\n";

// 3. ESPERAR EL CALLBACK
// El cliente escucha localmente en el 8082. Ngrok toma lo de internet y lo empuja aquí.
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
@socket_bind($listen_socket, "0.0.0.0", 8082); 
@socket_listen($listen_socket, 1);

$server_connection = @socket_accept($listen_socket);
if ($server_connection) {
    $notificacion = socket_read($server_connection, 1024);
    echo "\n[!!!] CALLBACK RECIBIDO DEL SERVIDOR: \n" . trim($notificacion) . "\n";
    socket_close($server_connection);
}
socket_close($listen_socket);
?>