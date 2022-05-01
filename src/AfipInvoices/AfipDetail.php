<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipInvoices;

use Multinexo\AfipValues\IvaConditionCodes;
use Multinexo\AfipValues\UnitSizeCodes;

/**
 * @deprecated
 */
class AfipDetail
{
    /**
     * @var float
     */
    private $qty = 1;

    /**
     * @var string
     */
    private $item_code;

    /**
     * @var float
     */
    private $qty_mtx = 1;

    /**
     * @var string
     */
    private $item_code_mtx = '';

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $unit_size_code = UnitSizeCodes::UNKNOWN;

    /**
     * @var float
     */
    private $item_net = 0;

    /**
     * @var float
     */
    private $bonus_amount = 0;
    /**
     * @var int
     */
    private $iva_condition_code = 5;

    public function getQty(): float
    {
        return $this->qty;
    }

    public function setQty(float $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getItemCode(): string
    {
        return $this->item_code;
    }

    public function setItemCode(string $item_code): self
    {
        $this->item_code = $item_code;

        return $this;
    }

    public function getQtyMtx(): float
    {
        return $this->qty_mtx;
    }

    public function setQtyMtx(float $qty_mtx): self
    {
        $this->qty_mtx = $qty_mtx;

        return $this;
    }

    public function getItemCodeMtx(): string
    {
        return $this->item_code_mtx;
    }

    public function setItemCodeMtx(string $item_code_mtx): self
    {
        $this->item_code_mtx = $item_code_mtx;

        return $this;
    }

    public function getUnitSizeCode(): int
    {
        return $this->unit_size_code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getItemNet(): float
    {
        return $this->item_net;
    }

    public function setItemNet(float $item_net): self
    {
        $this->item_net = $item_net;

        return $this;
    }

    public function getBonusAmount(): float
    {
        return $this->bonus_amount;
    }

    public function setBonusAmount(float $bonus): self
    {
        $this->bonus_amount = $bonus;

        return $this;
    }

    private function getIvaMultiplier(): float
    {
        return IvaConditionCodes::VALUES[$this->iva_condition_code] / 100;
    }

    public function getIvaConditionCode(): int
    {
        return $this->iva_condition_code;
    }

    public function setIvaConditionCode(int $iva_condition_code): self
    {
        $this->iva_condition_code = $iva_condition_code;

        return $this;
    }

    public function getNetAmount(): float
    {
        return $this->getQty() * $this->getItemNet();
    }

    public function getIvaAmount(): float
    {
        return ($this->getNetAmount() - $this->getBonusAmount()) * $this->getIvaMultiplier();
    }

    public function getItemAmount(): float
    {
        return ($this->getNetAmount() - $this->getBonusAmount()) * (1 + $this->getIvaMultiplier());
    }
}
