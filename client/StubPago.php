<?php
require_once __DIR__ . '/../shared/PagoDTO.php';

class StubPago {
    private $registryHost;
    private $registryPort;

    public function __construct($registryHost, $registryPort) {
        $this->registryHost = $registryHost;
        $this->registryPort = $registryPort;
    }

    public function procesarPago(PagoDTO $pago) {
        // 1. LOOKUP: Preguntar al Registry dónde está el servicio
        echo "[Stub] Consultando ubicación del servicio al Registry...\n";
        $r_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!@socket_connect($r_socket, $this->registryHost, $this->registryPort)) {
            return "[ERROR] Registry inalcanzable.";
        }
        
        $trama_lookup = "LOOKUP|ServicioPagos|EOF\n";
        socket_write($r_socket, $trama_lookup, strlen($trama_lookup));
        $resp_lookup = trim(socket_read($r_socket, 1024));
        socket_close($r_socket);

        if ($resp_lookup == "NOT_FOUND") return "[ERROR] Servicio no registrado.";
        
        // Extraemos IP y Puerto dinámicos
        list($ip_real, $puerto_real) = explode('|', str_replace("|EOF", "", $resp_lookup));

        // 2. COMUNICACIÓN FINAL (Ya con la IP obtenida)
        echo "[Stub] Conectando a servidor real en $ip_real:$puerto_real...\n";
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, $ip_real, $puerto_real);
        
        $payload = serialize($pago) . "|EOF\n";
        socket_write($socket, $payload, strlen($payload));
        $final_resp = socket_read($socket, 1024);
        socket_close($socket);

        return trim($final_resp);
    }
}