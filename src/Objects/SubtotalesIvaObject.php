<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Objects;

class SubtotalesIvaObject
{
    /** @var int */
    public $codigoIVA;
    /** @var float */
    public $importe;
    /** @var float */
    public $baseImponible;

    public static function create(int $codigo_iva, float $importe, float $base_imponible): self
    {
        $self = new self();
        $self->codigoIVA = $codigo_iva;
        $self->importe = $importe;
        $self->baseImponible = $base_imponible;

        return $self;
    }
}
