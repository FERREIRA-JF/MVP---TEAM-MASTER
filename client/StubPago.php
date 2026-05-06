<?php
// Archivo: client/StubPago.php
// Propósito: Ocultar la complejidad de red y empaquetar (Marshaling) el objeto.

require_once '../shared/PagoDTO.php';

class StubPago {
    private $host;
    private $port;

    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

    public function procesarPagoRemoto(PagoDTO $pago) {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!@socket_connect($socket, $this->host, $this->port)) {
            return "ERROR: No hay conexión con el servidor.";
        }

        // Marshaling: Convertimos el objeto en una cadena de bytes
        $payload_serializado = serialize($pago);
        
        // Ensamblamos la trama con nuestro delimitador
        $trama = "OBJETO|" . $payload_serializado . "|EOF\n";

        // Enviamos al servidor
        socket_write($socket, $trama, strlen($trama));

        // Esperamos respuesta
        $respuesta = socket_read($socket, 1024);
        socket_close($socket);

        return $respuesta;
    }
}
?>