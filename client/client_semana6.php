<?php
require_once 'StubPago.php';
require_once __DIR__ . '/../shared/PagoDTO.php';

echo "--- SISTEMA ACUDIENTES - TEAM MASTER (SEMANA 6) ---\n";

$miPago = new PagoDTO("ACU_001", 125000, "MENSUALIDAD_MAYO");

// ¡OJO AQUÍ! Ahora el Stub apunta al REGISTRY (Puerto 8081), ya no al servidor.
$ip_registry = "157.173.103.201"; 
$stub = new StubPago($ip_registry, 8081);

echo "Enviando objeto PagoDTO al sistema distribuido...\n";

// El Stub hará la magia: Buscará la IP en el Registry y luego enviará el pago
$resultado = $stub->procesarPago($miPago);

echo "[RESPUESTA FINAL] " . $resultado . "\n";
?>