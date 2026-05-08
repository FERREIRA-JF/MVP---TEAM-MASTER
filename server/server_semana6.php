<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

// Configuración del Servidor
$nombreServicio = "ServicioPagos";
$ipServidor = "157.173.103.201"; // Tu IP pública de Contabo
$puertoServidor = 8080;

// 1. REGISTRO AUTOMÁTICO (Bind) ante el Registry
echo "Anunciando servicio '$nombreServicio' al Registry...\n";
$registry_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (@socket_connect($registry_socket, "127.0.0.1", 8081)) {
    $trama_bind = "BIND|$nombreServicio|$ipServidor|$puertoServidor|EOF\n";
    socket_write($registry_socket, $trama_bind, strlen($trama_bind));
    socket_close($registry_socket);
    echo "[OK] Vinculado exitosamente.\n";
} else {
    die("[FATAL] El Registry no está activo. Abortando inicio.\n");
}

// 2. Inicio normal de escucha del servidor
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, "0.0.0.0", $puertoServidor);
socket_listen($socket, 5);

echo "=== SERVIDOR '$nombreServicio' EN ESCUCHA (8080) ===\n";

while (true) {
    $client_socket = socket_accept($socket);
    $payload = socket_read($client_socket, 1024);
    $datos_puros = str_replace("|EOF\n", "", $payload);
    $objetoRecibido = unserialize($datos_puros);

    if ($objetoRecibido instanceof PagoDTO) {
        echo "[PAGO RECIBIDO] Procesando $" . $objetoRecibido->monto . " de " . $objetoRecibido->idAcudiente . "\n";
        $respuesta = "TRANSACCION_EXITOSA|EOF\n";
    }

    socket_write($client_socket, $respuesta, strlen($respuesta));
    socket_close($client_socket);
}