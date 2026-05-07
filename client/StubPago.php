<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

class StubPago {
    private $host;
    private $port;

    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

    // El cliente llama a este método creyendo que es un proceso local
    public function procesarPago(PagoDTO $pago) {
        // 1. MARSHALING: Convertimos el objeto a un flujo de bytes/texto
        $datos_serializados = serialize($pago);
        
        // Empaquetamos con nuestro delimitador para evitar problemas en el buffer TCP
        $payload = $datos_serializados . "|EOF\n";

        // 2. Ocultamos la complejidad del socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $conexion = @socket_connect($socket, $this->host, $this->port);

        if (!$conexion) {
            return "[ERROR] El Stub no pudo conectar con el servidor.";
        }

        // Enviamos y esperamos respuesta
        socket_write($socket, $payload, strlen($payload));
        $respuesta = socket_read($socket, 1024);
        
        socket_close($socket);

        return trim($respuesta);
    }
}
?>