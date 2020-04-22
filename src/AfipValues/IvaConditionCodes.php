<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipValues;

/**
 * @see http://www.afip.gov.ar/genericos/guiavirtual/archivos/Ejemplo%20c%C3%B3mo%20se%20informan%20los%20distintos%20tipos%20de%20percepciones.pdf
 */
class IvaConditionCodes
{
    public const IVA_21 = 5;

    public const VALUES = [
        self::IVA_21 => 21,
    ];
}
