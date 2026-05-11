<?php
ini_set("display_errors", 0);
ini_set("soap.wsdl_cache_enabled", 0);

class TeamMasterAPI {
    private $db;

    // El constructor se ejecuta automáticamente e inicia la conexión a la BD
    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=teammaster_db;charset=utf8", "teammaster_user", "TeamMaster2026!");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error de BD: " . $e->getMessage());
            throw new SoapFault("Server", "Error interno de base de datos.");
        }
    }

    public function procesarPago($idAcudiente, $monto, $concepto) {
        if ($monto <= 0) {
            throw new SoapFault("Client", "El monto debe ser superior a cero.");
        }
        
        $estado = "APROBADO";
        $comprobante = "REC-" . time();

        try {
            // Guardamos el pago en la base de datos de forma segura
            $stmt = $this->db->prepare("INSERT INTO pagos (id_acudiente, monto, concepto, estado, comprobante) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$idAcudiente, $monto, $concepto, $estado, $comprobante]);
            
            error_log("[SOAP] Pago guardado en BD: $idAcudiente por $$monto");
            
            return [
                "estado" => $estado,
                "comprobante" => $comprobante
            ];
        } catch (PDOException $e) {
            error_log("Error al guardar pago: " . $e->getMessage());
            throw new SoapFault("Server", "No se pudo registrar el pago.");
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo "Servidor SOAP Operativo con conexión a Base de Datos.";
    exit;
}

$options = ['uri' => 'http://teammaster.online/soap'];
$server = new SoapServer(__DIR__ . '/../shared/teammaster.wsdl', $options);
$server->setClass('TeamMasterAPI');
$server->handle();