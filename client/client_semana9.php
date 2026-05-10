<?php
echo "--- CLIENTE TEAM MASTER - PROTOCOLO SOAP (WSDL/UDDI) ---\n";

// Actividad 3: Endpoint dinámico (Simulación UDDI)
$wsdl_url = "http://157.173.103.201:8080/shared/teammaster.wsdl";
try {
    // El cliente descarga el WSDL y "aprende" cómo hablar con el servidor
    $cliente = new SoapClient($wsdl_url, [
        'trace' => 1,
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);

    echo "[1] Consultando capacidades del servicio vía WSDL...\n";
    
    // Invocación del método remoto como si fuera local (Transparencia total)
    $params = [
        'idAcudiente' => 'ACU_009',
        'monto' => 50000,
        'concepto' => 'MENSUALIDAD_JULIO'
    ];

    echo "[2] Enviando petición SOAP...\n";
    $respuesta = $cliente->procesarPago($params['idAcudiente'], $params['monto'], $params['concepto']);

echo "[RESULTADO] Estado: " . $respuesta['estado'] . " | Recibo: " . $respuesta['comprobante'] . "\n";
} catch (SoapFault $e) {
    echo "[SOAP FAULT] Error del servidor: " . $e->getMessage() . "\n";
}