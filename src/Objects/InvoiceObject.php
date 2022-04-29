<?php

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
class InvoiceObject
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
    /** @var float */
    public  $importeGravado = .0;
    /** @var float */
    public  $importeOtrosTributos = .0;
    /** @var array|null */
    public  $comprobantesAsociados = null;
    /** @var array|null */
    public  $arraySubtotalesIVA = [];
}
