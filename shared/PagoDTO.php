<?php
// Data Transfer Object (DTO) para manejar los pagos de Team Master
class PagoDTO {
    public $idAcudiente;
    public $monto;
    public $concepto;

    public function __construct($idAcudiente, $monto, $concepto) {
        $this->idAcudiente = $idAcudiente;
        $this->monto = $monto;
        $this->concepto = $concepto;
    }
}
?>