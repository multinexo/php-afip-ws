<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipInvoices;

use Multinexo\AfipValues\IdCodes;
use Multinexo\AfipValues\ReceiptCodes;
use Multinexo\Models\AfipConfig;
use Multinexo\WSMTXCA\AfipResult;
use Multinexo\WSMTXCA\Wsmtxca;

class AfipInvoice
{
    /** @var int */
    private $pos_number = 1;

    /**
     * @var int
     */
    private $receipt_code = ReceiptCodes::FACTURA_A;

    /**
     * @var int
     */
    private $receipt_number = 1;

    /**
     * @var int
     */
    private $id_code = IdCodes::NO_ID;

    /**
     * @var int
     */
    private $id_number = 0;

    /** @var AfipDetail[] */
    private $details = [];

    public function addAfipDetail(AfipDetail $detail): void
    {
        $this->details[] = $detail;
    }

    public function createAfipDetail(): AfipDetail
    {
        $detail = new AfipDetail();
        $this->addAfipDetail($detail);

        return $detail;
    }

    public function getPosNumber(): int
    {
        return $this->pos_number;
    }

    public function setPosNumber(int $pos_number): self
    {
        $this->pos_number = $pos_number;

        return $this;
    }

    public function isLetterB(): bool
    {
        return in_array($this->getReceiptCode(), [
            ReceiptCodes::FACTURA_B,
            ReceiptCodes::NOTA_DEBITO_B,
            ReceiptCodes::NOTA_CREDITO_B,
        ], true);
    }

    public function getReceiptCode(): int
    {
        return $this->receipt_code;
    }

    public function setReceiptCode(int $afip_receipt_code): self
    {
        $this->receipt_code = $afip_receipt_code;

        return $this;
    }

    public function getReceiptNumber(): int
    {
        return $this->receipt_number;
    }

    public function setReceiptNumber(int $receipt_number): self
    {
        $this->receipt_number = $receipt_number;

        return $this;
    }

    public function getIdCode(): int
    {
        return $this->id_code;
    }

    public function setIdCode(int $id_code): self
    {
        $this->id_code = $id_code;

        return $this;
    }

    public function getIdNumber(): int
    {
        return $this->id_number;
    }

    public function setIdNumber(int $id_number): self
    {
        $this->id_number = $id_number;

        return $this;
    }

    /**
     * @return AfipDetail[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public function getExemptAmount(): float
    {
        return .0;
    }

    public function getAnotherTaxes(): float
    {
        return .0;
    }

    public function getDataFromAfip(AfipConfig $config): AfipResult
    {
        // legacy bootstrap
        $factura = new Wsmtxca($config);

        /** @todo change to object? */
        /** @phpstan-ignore-next-line */
        $factura->datos = (new AfipInvoiceTranslator($this))->getDataWsmtxcaArray();

        return new AfipResult();
    }
}
