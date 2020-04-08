<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipValues;

class IdCodes
{
    /**
     * AFIP have more old options.
     */
    public const CUIT = 80;
    public const CUIL = 86;
    public const PASSPORT = 94;
    public const DNI = 96;
    public const NO_ID = 99;
}
