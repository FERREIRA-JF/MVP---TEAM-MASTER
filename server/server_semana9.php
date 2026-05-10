<?php
// Clase que contiene la lógica de negocio
class TeamMasterAPI {
    public function procesarPago($idAcudiente, $monto, $concepto) {
        if ($monto <= 0) {
            // Actividad 2: SOAP Fault para errores estandarizados
            throw new SoapFault("Client", "El monto debe ser superior a cero.");
        }
        
        echo "[SOAP] Procesando pago de $idAcudiente por $$monto ($concepto)\n";
        
        return [
            "estado" => "APROBADO",
            "comprobante" => "REC-" . time()
        ];
    }
}

// Configuración del Servidor SOAP
$options = ['uri' => 'http://teammaster.online/soap'];
$server = new SoapServer(__DIR__ . '/../shared/teammaster.wsdl', $options);
$server->setClass('TeamMasterAPI');

echo "=== SERVIDOR SOAP TEAM MASTER INICIADO ===\n";
// En un entorno real, esto corre sobre Apache/Nginx. 
// Para la práctica de sockets, el servidor procesará la petición POST.
$server->handle();