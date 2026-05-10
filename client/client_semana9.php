<?php
// Forzamos a PHP a mostrar errores y limpiar caché
ini_set("display_errors", 1);
error_reporting(E_ALL);
ini_set("soap.wsdl_cache_enabled", 0); 

echo "--- CLIENTE TEAM MASTER - PRODUCCION ---\n";
$wsdl_url = "http://157.173.103.201/shared/teammaster.wsdl";

try {
    $cliente = new SoapClient($wsdl_url, [
        'trace' => 1,
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);

    echo "[1] Consultando capacidades del servicio vía WSDL...\n";
    
    $params = [
        'idAcudiente' => 'ACU_009',
        'monto' => 175000.0,
        'concepto' => 'MENSUALIDAD_JULIO'
    ];

    echo "[2] Enviando peticion SOAP...\n";
    $respuesta = $cliente->procesarPago($params['idAcudiente'], $params['monto'], $params['concepto']);

    echo "[RESULTADO] Estado: " . $respuesta['estado'] . " | Recibo: " . $respuesta['comprobante'] . "\n";

} catch (SoapFault $e) {
    echo "[SOAP FAULT] Error del servidor: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "[ERROR FATAL] " . $e->getMessage() . "\n";
}