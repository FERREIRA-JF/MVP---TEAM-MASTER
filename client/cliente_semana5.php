<?php
require_once 'StubPago.php';
require_once __DIR__ . '/../shared/PagoDTO.php';

echo "--- SISTEMA ACUDIENTES - TEAM MASTER (SEMANA 5) ---\n";

// 1. Instanciamos nuestro objeto de negocio de forma normal
$miPago = new PagoDTO("ACU_001", 125000, "MENSUALIDAD_MAYO");

// 2. Instanciamos el Stub (Cambia esta IP a la de tu VPS en Contabo: "157.173.103.201")
$ip_servidor = "157.173.103.201"; 
$stub = new StubPago($ip_servidor, 8080);

echo "Enviando objeto PagoDTO al servidor mediante Stub...\n";

// 3. ¡Magia! Llamamos al método como si el servidor estuviera en nuestra computadora
$resultado = $stub->procesarPago($miPago);

echo "[RESPUESTA DEL SERVIDOR] " . $resultado . "\n";
?>