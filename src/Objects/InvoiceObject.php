<?php

namespace Multinexo\Objects;

/**
 * @property int $puntoVenta
 * @property int $codigoComprobante
 * @property int $codigoConcepto
 * @property int $codigoDocumento
 * @property int $numeroDocumento
 * @property int $numeroComprobante // todo: depende de la cantidad de fact enviadas
 * @property string $fechaEmision
 * @property int $importeTotal
 * @property int $importeGravado
 * @property int $importeNoGravado
 * @property int $importeExento
 * @property int $importeIVA
 */
class InvoiceObject extends \stdClass
{
    /** @var int */
    public  $cantidadRegistros = 1;
    /** @var string */
    public  $codigoMoneda = 'PES';
    /** @var int */
    public  $cotizacionMoneda = 1;
    /** @var int */
    public  $fechaServicioDesde = null;
    /** @var int */
    public  $fechaServicioHasta = null;
    /** @var int */
    public  $fechaVencimientoPago = null;
    /** @var array|null */
    public  $arrayComprobantesAsociados = null;
}
