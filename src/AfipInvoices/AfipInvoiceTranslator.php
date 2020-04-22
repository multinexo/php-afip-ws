<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipInvoices;

use Illuminate\Support\Arr;

/**
 * @internal
 */
final class AfipInvoiceTranslator
{
    /** @var AfipInvoice */
    private $invoice;
    private $subtotal_iva_by_code = [];
    private $items = [];

    public function __construct(AfipInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    private function getDataArray(): array
    {
        $importeGravado = 0;
        $importeNoGravado = 0;
        $total_iva = 0;
        $total_net = 0;
        foreach ($this->invoice->getDetails() as $detail) {
            $total_iva += $detail->getIvaAmount();
            $total_net += $detail->getNetAmount();

            $afip_detail_translator = new AfipDetailTranslator($detail);
            if ($this->invoice->isLetterB()) {
                $this->items[] = $afip_detail_translator->translateWithoutIva();
            } else {
                $this->items[] = $afip_detail_translator->translateWithIva();
            }

            $importeGravado += $detail->getNetAmount();

            $this->addIvaAmount($detail);
        }

        $subtotal = $this->invoice->getExemptAmount() + $importeGravado + $importeNoGravado;
        $total = $subtotal + $this->invoice->getAnotherTaxes() + $total_iva;

        if ($this->invoice->isLetterB()) {
            $subtotal = $total_net;
            $importeGravado = $total_net;
        }

        return [
            'cantidadRegistros' => 1,
            'puntoVenta' => $this->invoice->getPosNumber(),
            'codigoComprobante' => $this->invoice->getReceiptCode(),
            'numeroComprobante' => $this->invoice->getReceiptNumber(),
            'codigoConcepto' => 1,
            'codigoDocumento' => $this->invoice->getIdCode(),
            'numeroDocumento' => $this->invoice->getIdNumber(),
            'codigoMoneda' => 'PES',
            'cotizacionMoneda' => 1,
            'importeGravado' => $importeGravado,
            'importeNoGravado' => $importeNoGravado,
            'importeExento' => $this->invoice->getExemptAmount(),
            'importeOtrosTributos' => $this->invoice->getAnotherTaxes(),
            'importeSubtotal' => $subtotal,
            'importeIVA' => $total_iva,
            'importeTotal' => $total,
            'fechaServicioDesde' => null,
            'fechaServicioHasta' => null,
            'fechaVencimientoPago' => null,
            'arrayComprobantesAsociados' => null,
        ];
    }

    public function getDataWsmtxcaArray(): array
    {
        $data = $this->getDataArray();

        $data['fechaEmision'] = date('Y-m-d');
        $data['codigoTipoAutorizacion'] = null;
        $data['observaciones'] = null;
        $data['arrayItems'] = ['item' => $this->items];
        $data['arraySubtotalesIVA'] = $this->getIvaAmounts(false);
        if ($this->invoice->isLetterB()) {
            unset($data['importeIVA']);
        }

        // Por Ahora no se esta usando
        // Arr::forget($data, ['importeOtrosTributos', 'arrayComprobantesAsociados', 'arrayOtrosTributos']);

        return $data;
    }

    public function getDataWsfeArray(): array
    {
        $data = $this->getDataArray();

        $data['fechaEmision'] = date('Ymd');
        $data['arraySubtotalesIVA'] = $this->getIvaAmounts(true);

        // Entity is a final costumer AND, because importeIVA==0, its a C invoice.
        // Only document C (not B) requires net=total (kiosko kiosko client case).
        if ($data['codigoDocumento'] == 99 && $data['importeIVA'] == 0) {
            $data['importeGravado'] = $data['importeSubtotal'] = $data['importeTotal'];
        }

        return $data;
    }

    private function addIvaAmount(AfipDetail $detail): void
    {
        $iva_code = $detail->getIvaConditionCode();

        if (!isset($this->subtotal_iva_by_code[$iva_code])) {
            $this->subtotal_iva_by_code[$iva_code] = [
                'iva_amount' => .0,
                'net_amount' => .0,
            ];
        }

        $this->subtotal_iva_by_code[$iva_code]['iva_amount'] += $detail->getIvaAmount();
        $this->subtotal_iva_by_code[$iva_code]['net_amount'] += $detail->getNetAmount();
    }

    private function getIvaAmounts(bool $is_wsfe_service): array
    {
        $data = [];
        foreach ($this->subtotal_iva_by_code as $condition_code => $value) {
            $data[] = [
                'codigoIva' => $condition_code,
                'importe' => $value['iva_amount'],
                'baseImponible' => $is_wsfe_service ? $value['net_amount'] : null,
            ];
        }

        return [
            'subtotalIVA' => $data,
        ];
    }
}
