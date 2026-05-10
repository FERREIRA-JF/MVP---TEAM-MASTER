<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

echo "--- SISTEMA ACUDIENTES - MODO ASINCRONO ---\n";

$miPago = new PagoDTO("ACU_002", 85000, "DOTACION_DEPORTIVA");

// Configuración del Callback del Cliente (Laragon)
// IMPORTANTE: Para que el servidor en Contabo pueda llamarte, necesitas tu IP Pública de internet, 
// o usar una IP simulada si estás detrás de un NAT sin puertos abiertos.
// Por simplicidad educativa en esta guía, usaremos un truco: 
// Haremos que el cliente espere la respuesta, simulando que su puerto local está abierto.

$ip_registry = "157.173.103.201"; 
$mi_puerto_callback = 8082; // Puerto donde el cliente esperará

// 1. Lookup manual al Registry
$r_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
@socket_connect($r_socket, $ip_registry, 8081);
socket_write($r_socket, "LOOKUP|ServicioPagosAsincrono|EOF\n");
$resp_lookup = trim(socket_read($r_socket, 1024));
socket_close($r_socket);

list($ip_real, $puerto_real) = explode('|', str_replace("|EOF", "", $resp_lookup));

// 2. Envío del Pago + Datos de Callback
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, $ip_real, $puerto_real);

// OJO: Enviamos nuestra IP pública (puedes ver la tuya buscando "Cual es mi ip" en Google)
// Si no tienes el puerto abierto en tu router de Cúcuta, el servidor fallará al llamarte.
// Para propósitos de la captura de la universidad, pondremos una IP falsa de demostración
// y capturaremos el intento del servidor.
$mi_ip_publica = "190.143.29.96"; // Reemplaza con tu IP real de Cúcuta si deseas

$payload = serialize($miPago) . "|$mi_ip_publica|$mi_puerto_callback|EOF\n";
socket_write($socket, $payload, strlen($payload));

// Recibimos el "RECIBIDO_PROCESANDO"
$ack = socket_read($socket, 1024);
socket_close($socket);

echo "[1] Respuesta del Servidor: " . trim($ack) . "\n";
echo "[2] Abriendo puerto local $mi_puerto_callback para esperar notificación...\n";

// 3. El Cliente se convierte en "Servidor" para esperar el Callback
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
@socket_bind($listen_socket, "0.0.0.0", $mi_puerto_callback);
@socket_listen($listen_socket, 1);

echo "    (Puedes seguir usando el sistema mientras esperas)\n";

$server_connection = @socket_accept($listen_socket);
if ($server_connection) {
    $notificacion = socket_read($server_connection, 1024);
    echo "\n[!!!] CALLBACK RECIBIDO DEL SERVIDOR: \n" . trim($notificacion) . "\n";
    socket_close($server_connection);
}
socket_close($listen_socket);
?>