<?php
// server_semana8.php - Servidor con Validación XML y XPath
$host = "0.0.0.0";
$port = 8080;
$xsd_file = __DIR__ . '/../shared/protocolo_teammaster.xsd';

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket, 5);

echo "=== SERVIDOR XML TEAM MASTER ACTIVO ===\n";

while (true) {
    $client = socket_accept($socket);
    $xml_data = socket_read($client, 2048);
    $xml_data = str_replace("|EOF\n", "", $xml_data);

    // 1. VALIDACIÓN ESTRUCTURAL (Actividad 2)
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    if (!$dom->loadXML($xml_data) || !$dom->schemaValidate($xsd_file)) {
        $respuesta = "<Error><mensaje>Estructura XML Invalida</mensaje></Error>";
        echo "[ERROR] XML recibido no cumple con el esquema XSD.\n";
    } else {
        // 2. EXTRACCIÓN CON XPATH (Actividad 4)
        $xpath = new DOMXPath($dom);
        $operacion = $xpath->query("//operacion")->item(0)->nodeValue;
        $monto = $xpath->query("//monto")->item(0)->nodeValue;
        $acudiente = $xpath->query("//id_acudiente")->item(0)->nodeValue;

        echo "[OK] XML Valido. Operacion: $operacion | Monto: $monto | Acudiente: $acudiente\n";
        
        // Respuesta en formato XML
        $respuesta = "<?xml version='1.0' encoding='UTF-8'?>
                      <Respuesta>
                        <estado>EXITOSO</estado>
                        <detalles>Pago procesado para $acudiente</detalles>
                        <monto_final>$monto</monto_final>
                      </Respuesta>";
    }

    socket_write($client, $respuesta . "|EOF\n", strlen($respuesta) + 5);
    socket_close($client);
}