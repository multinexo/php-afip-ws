<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests;

use Multinexo\Objects\InvoiceObject;

trait InvoiceTestTrait
{
    private static function getInvoiceData(
        $codigoComprobante,
        $importeTotal,
        $importeGravado,
        $importeSubtotal,
        $importeNoGravado,
        $importeIVA = .0,
        $importTribute = .0
    ): InvoiceObject {
        $invoice = new InvoiceObject();
        $invoice->cantidadRegistros = 1;
        $invoice->puntoVenta = 3;
        $invoice->codigoComprobante = $codigoComprobante;
        $invoice->codigoConcepto = 1;
        $invoice->codigoDocumento = 80;
        $invoice->numeroDocumento = 20327936221;
        $invoice->fechaEmision = date('Y-m-d');
        $invoice->importeGravado = $importeGravado;
        $invoice->importeNoGravado = $importeNoGravado;
        $invoice->importeExento = 0;
        $invoice->importeSubtotal = $importeSubtotal;
        $invoice->importeIVA = $importeIVA;
        $invoice->importeTotal = $importeTotal;
        $invoice->importeOtrosTributos = $importTribute;
        $invoice->fechaServicioDesde = '2016-03-16';
        $invoice->fechaServicioHasta = '2016-03-16';
        $invoice->fechaVencimientoPago = '2016-03-16';

        return $invoice;
    }
}
