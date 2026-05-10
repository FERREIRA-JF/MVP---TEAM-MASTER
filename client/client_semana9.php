<?php
ini_set("display_errors", 0);
ini_set("soap.wsdl_cache_enabled", 0); // Destruye el caché en Ubuntu

class TeamMasterAPI {
    public function procesarPago($idAcudiente, $monto, $concepto) {
        if ($monto <= 0) {
            throw new SoapFault("Client", "El monto debe ser superior a cero.");
        }
        
        // Log interno del servidor
        error_log("[SOAP] Pago exitoso en produccion: $idAcudiente por $$monto");
        
        return [
            "estado" => "APROBADO",
            "comprobante" => "REC-" . time()
        ];
    }
}

// Si alguien entra por el navegador web (GET), le mostramos un mensaje en vez de un error
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo "Servidor SOAP Operativo. Esperando peticiones POST del cliente Team Master.";
    exit;
}

$options = ['uri' => 'http://teammaster.online/soap'];
$server = new SoapServer(__DIR__ . '/../shared/teammaster.wsdl', $options);
$server->setClass('TeamMasterAPI');
$server->handle();