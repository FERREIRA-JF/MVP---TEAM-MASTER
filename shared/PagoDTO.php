<?php
// Archivo: shared/PagoDTO.php
// Propósito: Objeto de Transferencia de Datos (DTO) que viajará por la red.

class PagoDTO {
    public $id_acudiente;
    public $monto;
    public $metodo_pago;
    public $fecha;

    public function __construct($id, $monto, $metodo) {
        $this->id_acudiente = $id;
        $this->monto = $monto;
        $this->metodo_pago = $metodo;
        $this->fecha = date('Y-m-d H:i:s');
    }
}
?>