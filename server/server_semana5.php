<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

$host = "0.0.0.0"; // Escucha universal
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket, 5);

echo "=== SERVIDOR TEAM MASTER ACTIVO (SEMANA 5) ===\n";
echo "Esperando objetos serializados en $host:$port...\n";

while (true) {
    $client_socket = socket_accept($socket);
    echo "\n[+] Nueva conexion entrante aceptada.\n";

    // Recibimos la trama del Stub
    $payload = socket_read($client_socket, 1024);

    // Limpiamos el delimitador para extraer los bytes puros
    $datos_puros = str_replace("|EOF\n", "", $payload);

    // 3. UNMARSHALING: Reconstrucción del objeto
    $objetoRecibido = unserialize($datos_puros);

    // Validamos que la reconstrucción fue exitosa comprobando el tipo de clase
    if ($objetoRecibido instanceof PagoDTO) {
        echo "[RECIBIDO] Objeto PagoDTO reconstruido exitosamente (Unmarshaling).\n";
        echo "   -> Acudiente: " . $objetoRecibido->idAcudiente . "\n";
        echo "   -> Monto COP: $" . $objetoRecibido->monto . "\n";
        echo "   -> Concepto: " . $objetoRecibido->concepto . "\n";

        $respuesta = "TRANSACCION_EXITOSA|OBJETO_PROCESADO|EOF\n";
    } else {
        echo "[ERROR] La trama recibida no se pudo reconstruir en un objeto.\n";
        $respuesta = "ERROR_SERIALIZACION|EOF\n";
    }

    socket_write($client_socket, $respuesta, strlen($respuesta));
    socket_close($client_socket);
    echo "[-] Conexion con el cliente cerrada.\n";
}
socket_close($socket);
?>