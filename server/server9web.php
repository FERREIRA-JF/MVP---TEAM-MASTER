<?php
// Evitamos que errores de advertencia rompan el XML del sobre SOAP
ini_set("display_errors", 0);
ini_set("soap.wsdl_cache_enabled", 0);

class TeamMasterAPI {
    private $db;

    // Constructor: Se conecta a la BD apenas se instancia la clase
    public function __construct() {
        try {
            // Usamos PDO con charset utf8 para evitar problemas con tildes
            $dsn = "mysql:host=localhost;dbname=teammaster_db;charset=utf8";
            $this->db = new PDO($dsn, "teammaster_user", "TeamMaster2026!");
            
            // Configuramos PDO para que lance excepciones en caso de error
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error de Conexión BD: " . $e->getMessage());
            throw new SoapFault("Server", "Error interno: El servicio de datos no está disponible.");
        }
    }

    public function procesarPago($idAcudiente, $monto, $concepto) {
        // Validaciones de regla de negocio
        if ($monto <= 0) {
            throw new SoapFault("Client", "El monto debe ser superior a cero.");
        }
        
        $estado = "APROBADO";
        $comprobante = "REC-" . time();

        try {
            // PREPARED STATEMENTS: La mejor práctica para evitar Inyección SQL
            $sql = "INSERT INTO pagos (id_acudiente, monto, concepto, estado, comprobante) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idAcudiente, $monto, $concepto, $estado, $comprobante]);
            
            error_log("[DATABASE] Registro exitoso para el acudiente: $idAcudiente");
            
            return [
                "estado" => $estado,
                "comprobante" => $comprobante
            ];
        } catch (PDOException $e) {
            error_log("Error en INSERT: " . $e->getMessage());
            throw new SoapFault("Server", "No se pudo registrar la transacción en la base de datos.");
        }
    }
}

// Lógica para el servidor SOAP
$options = ['uri' => 'http://teammaster.online/soap'];
$server = new SoapServer(__DIR__ . '/../shared/teammaster.wsdl', $options);
$server->setClass('TeamMasterAPI');
$server->handle();