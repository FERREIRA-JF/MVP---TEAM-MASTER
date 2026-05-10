<?php
echo "--- CLIENTE TEAM MASTER - PROTOCOLO XML ---\n";

// Datos del Pago
$operacion = "REGISTRAR_PAGO";
$acudiente = "ACU_008";
$monto = 150000;
$concepto = "MENSUALIDAD_JUNIO";

// 1. CONSTRUCCIÓN DEL XML (Actividad 1)
$xml = new SimpleXMLElement('<MensajeTeamMaster/>');
$cabecera = $xml->addChild('cabecera');
$cabecera->addChild('operacion', $operacion);
$cabecera->addChild('timestamp', date('Y-m-d H:i:s'));

$cuerpo = $xml->addChild('cuerpo');
$cuerpo->addChild('id_acudiente', $acudiente);
$cuerpo->addChild('monto', $monto);
$cuerpo->addChild('concepto', $concepto);

$payload = $xml->asXML() . "|EOF\n";

// 2. ENVÍO AL VPS
$ip_vps = "157.173.103.201"; 
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (@socket_connect($socket, $ip_vps, 8080)) {
    socket_write($socket, $payload, strlen($payload));
    $respuesta = socket_read($socket, 2048);
    echo "[SERVIDOR] " . str_replace("|EOF\n", "", $respuesta) . "\n";
} else {
    echo "[ERROR] No se pudo conectar al servidor.\n";
}
socket_close($socket);