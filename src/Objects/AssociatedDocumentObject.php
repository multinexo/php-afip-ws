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
 * @see https://github.com/AfipSDK/afip.php/issues/82#issuecomment-819823603
 */
class AssociatedDocumentObject
{
    /** @var int */
    public $tipo;
    /** @var int */
    public $punto_de_venta;
    /** @var int */
    public $numero_comprobante;

    public static function create(int $tipo, int $punto_de_venta, int $numero_comprobante): self
    {
        $self = new self();
        $self->tipo = $tipo;
        $self->punto_de_venta = $punto_de_venta;
        $self->numero_comprobante = $numero_comprobante;

        return $self;
    }
}
