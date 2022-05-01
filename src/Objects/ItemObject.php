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
 * @property string $codigoMtx
 * @property string $codigo
 * @property string $descripcion
 * @property int $codigoUnidadMedida
 * @property float $precioUnitario
 * @property float $importeBonificacion
 * @property int $codigoCondicionIVA
 * @property float $importeIVA
 * @property float $importeItem
 */
class ItemObject extends \stdClass
{
    /** @var int */
    public $unidadesMtx = 1;
    /** @var float */
    public $cantidad = 1;
}
