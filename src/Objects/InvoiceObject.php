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
 * @property int $numeroComprobante // todo: depende de la cantidad de fact enviadas
 * @property string $fechaEmision Format Ymd
 * @property flaot $importeTotal
 * @property flaot $importeNoGravado
 * @property int $importeExento
 * @property float $importeIVA
 */
class InvoiceObject extends \stdClass
{
    /** @var int */
    public $cantidadRegistros = 1;
    /** @var string */
    public $codigoMoneda = 'PES';
    /** @var int */
    public $cotizacionMoneda = 1;
    /** @var int */
    public $fechaServicioDesde;
    /** @var int */
    public $fechaServicioHasta;
    /** @var int */
    public $fechaVencimientoPago;
    /** @var float */
    public $importeGravado = .0;
    /** @var float */
    public $importeOtrosTributos = .0;
    /** @var array|null */
    public $comprobantesAsociados;
    /** @var array|null */
    public $arraySubtotalesIVA = [];
}
