<?php
// Clase que contiene la lógica de negocio
class TeamMasterAPI {
    public function procesarPago($idAcudiente, $monto, $concepto) {
        if ($monto <= 0) {
            // Actividad 2: SOAP Fault para errores estandarizados
            throw new SoapFault("Client", "El monto debe ser superior a cero.");
        }
        
        // Usamos error_log para imprimir en la terminal del VPS, NO echo
        error_log("[SOAP] Procesando pago de $idAcudiente por $$monto ($concepto)");
        
        return [
            "estado" => "APROBADO",
            "comprobante" => "REC-" . time()
        ];
    }
}

// Ocultamos advertencias de PHP para no ensuciar el XML
ini_set("display_errors", 0);

// Configuración del Servidor SOAP
$options = ['uri' => 'http://teammaster.online/soap'];
$server = new SoapServer(__DIR__ . '/../shared/teammaster.wsdl', $options);
$server->setClass('TeamMasterAPI');

error_log("=== SERVIDOR SOAP TEAM MASTER ESCUCHANDO EN HTTP ===");
$server->handle();