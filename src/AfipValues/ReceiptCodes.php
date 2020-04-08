<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\AfipValues;

class ReceiptCodes
{
    public const FACTURA_A = 1;
    public const NOTA_DEBITO_A = 2;
    public const FACTURA_B = 6;
    public const NOTA_DEBITO_B = 7;
    public const NOTA_CREDITO_B = 8;
    public const FACTURA_C = 11;
    public const NOTA_CREDITO_A = 3;
    public const NOTA_CREDITO_C = 13;
    public const CIERRE_Z = 58;
    public const REMITO_R = 69;
    public const PRESUPUESTO = 81;
    public const REMITO_STOCK = 82;
    public const DOCUMENT_NO_FISCAL = 83;
    public const FACTURA_DE_EXPORTACION = 17;
}
