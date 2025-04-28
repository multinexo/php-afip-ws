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
 * ONLY READ PROPERTIES.
 *
 * @property \stdClass $FeDetReq
 * @property \stdClass $otroTributo
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class InvoiceObject extends FiscalDocumentDto
{
    /** @var ?int */
    public $puntoVenta;
    /** @var ?int */
    public $codigoComprobante;
    /** @var ?int */
    public $codigoConcepto;
    /** @var ?int */
    public $codigoDocumento;
    /** @var ?int */
    public $numeroDocumento;
    /** @var ?string */
    public $fechaEmision; //  Format Y-m-d
    /** @var ?float */
    public $importeTotal;
    /** @var ?float */
    public $importeNoGravado;
    /** @var ?float */
    public $importeExento;
    /** @var ?float */
    public $importeIVA;
    /** @var ?string */
    public $codigoTipoAutorizacion;
    /** @var int */
    public $cantidadRegistros = 1;
    /** @var int|null */
    public $numeroComprobante;
    /** @var string */
    public $codigoMoneda = 'PES';
    /** @var int */
    public $cotizacionMoneda = 1;
    /** @var ?int */
    public $condicionIVAReceptorId;
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
