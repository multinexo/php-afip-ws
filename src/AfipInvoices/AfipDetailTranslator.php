<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipInvoices;

/**
 * @internal
 */
class AfipDetailTranslator
{
    /** @var AfipDetail */
    private $detail;

    public function __construct(AfipDetail $detail)
    {
        $this->detail = $detail;
    }

    private function translate(): array
    {
        return [
            'unidadesMtx' => $this->detail->getQtyMtx(),
            'codigoMtx' => $this->detail->getItemCodeMtx(),
            'codigo' => $this->detail->getItemCode(),
            'descripcion' => $this->detail->getDescription(),
            'cantidad' => $this->detail->getQty(),
            'codigoUnidadMedida' => $this->detail->getUnitSizeCode(),
            'precioUnitario' => $this->detail->getItemNet(),
            'importeBonificacion' => $this->detail->getBonusAmount(),
            'codigoCondicionIVA' => $this->detail->getIvaConditionCode(),
            'importeIVA' => $this->detail->getIvaAmount(),
            'importeItem' => $this->detail->getItemAmount(),
        ];
    }

    public function translateWithIva(): array
    {
        return $this->translate();
    }

    public function translateWithoutIva(): array
    {
        $data = $this->translate();

        if ($this->detail->getQty() > 0) {
            $item['precioUnitario'] = ($this->detail->getIvaAmount() + $this->detail->getBonusAmount()) / $this->detail->getQty();
        }
        unset($item['importeIVA']);

        return $data;
    }
}
