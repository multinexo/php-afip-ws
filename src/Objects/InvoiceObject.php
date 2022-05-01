<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Objects;

/**
 * @property int $puntoVenta
 * @property int $codigoComprobante
 * @property int $codigoConcepto
 * @property int $codigoDocumento
 * @property int $numeroDocumento
 * @property string $fechaEmision Format Y-m-d
 * @property float $importeTotal
 * @property float $importeNoGravado
 * @property int $importeExento
 * @property float $importeIVA
 * @property string|null $codigoTipoAutorizacion
 * ONLY READ PROPERTIES
 * @property \stdClass $FeDetReq
 * @property \stdClass $otroTributo
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class InvoiceObject extends DocumentObject
{
    /** @var int */
    public $cantidadRegistros = 1;
    /** @var int|null */
    public $numeroComprobante;
    /** @var string */
    public $codigoMoneda = 'PES';
    /** @var int */
    public $cotizacionMoneda = 1;
    /** @var string */
    public $fechaServicioDesde;
    /** @var string */
    public $fechaServicioHasta;
    /** @var string */
    public $fechaVencimientoPago;
    /** @var float */
    public $importeGravado = .0;
    /** @var float */
    public $importeOtrosTributos = .0;
    /** @var array */
    public $arrayOtrosTributos = [];
    /** @var float */
    public $importeSubtotal = .0;
    /** @var AssociatedDocumentObject[] */
    public $comprobantesAsociados = [];
    /** @var SubtotalesIvaObject[] */
    public $subtotalesIVA = [];
    /** @var \stdClass[] */
    public $opcionales = [];
    /** @var string */
    public $observaciones = '';
    /** @var array */
    public $items = [];

    public function clean(): void
    {
        $this->codigoTipoAutorizacion = null; // no debe informarse
    }
}
