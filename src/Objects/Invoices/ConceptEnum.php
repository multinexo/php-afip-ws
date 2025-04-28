<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Objects\Invoices;

/**
 * https://www.arca.gob.ar/ws/documentacion/manuales/manual-desarrollador-ARCA-COMPG-v4-0.pdf.
 */
class ConceptEnum
{
    public const PRODUCTOS = 1;
    public const SERVICIOS = 2;
    public const PRODUCTOS_SERVICIOS = 3;
}
