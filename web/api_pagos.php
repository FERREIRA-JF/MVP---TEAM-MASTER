<?php
// Configuramos los encabezados para que el navegador sepa que responderemos con JSON
header('Content-Type: application/json');
ini_set("display_errors", 0);
ini_set("soap.wsdl_cache_enabled", 0);

// 1. Recibir los datos crudos en formato JSON desde app.js
$jsonInput = file_get_contents('php://input');
$datos = json_decode($jsonInput, true);

// Validación básica de seguridad
if (!$datos || !isset($datos['idAcudiente']) || !isset($datos['monto']) || !isset($datos['concepto'])) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Datos de pago incompletos o inválidos.'
    ]);
    exit;
}

// 2. Conectarse al contrato WSDL en PRODUCCIÓN usando tu dominio oficial
// Esto garantiza que el frontend siempre encuentre el backend, sin importar si cambia la IP del VPS
$wsdl_url = "http://cecifc.masterteam.online/shared/teammaster.wsdl";

try {
    $clienteSoap = new SoapClient($wsdl_url, [
        'trace' => 1,
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);

    // 3. Ejecutar el método remoto SOAP
    $respuestaSoap = $clienteSoap->procesarPago(
        $datos['idAcudiente'], 
        $datos['monto'], 
        $datos['concepto']
    );

    // 4. Devolver éxito al Frontend en formato JSON
    echo json_encode([
        'exito' => true,
        'estado' => $respuestaSoap['estado'],
        'comprobante' => $respuestaSoap['comprobante']
    ]);

} catch (SoapFault $e) {
    // 5. Devolver errores de negocio (Ej: monto negativo) capturados desde el SOAP Fault
    echo json_encode([
        'exito' => false,
        'mensaje' => "Error del servicio: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Errores graves (Servidor caído, WSDL no encontrado)
    echo json_encode([
        'exito' => false,
        'mensaje' => "Fallo en la comunicación con el núcleo de Team Master."
    ]);
}