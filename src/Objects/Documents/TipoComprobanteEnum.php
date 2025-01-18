<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Objects\Documents;

/**
 * @see https://www.afip.gob.ar/canasta-alimentaria/documentos/tipos-de-comprobantes-de-ventas.pdf
 */
class TipoComprobanteEnum
{
    public const FACTURA_A = 1;
    public const NOTA_DEBITO_A = 2;
    public const NOTA_CREDITO_A = 3;
    public const RECIBO_A = 4;
    public const NOTA_VENTA_AL_CONTADO_A = 5;
    public const FACTURA_B = 6;
    public const NOTA_DEBITO_B = 7;
    public const NOTA_CREDITO_B = 8;
    public const RECIBO_B = 9;
    public const NOTA_VENTA_AL_CONTADO_B = 10;
    public const FACTURA_C = 11;
    public const NOTA_DEBITO_C = 12;
    public const NOTA_CREDITO_C = 13;

    /** @deprecated was changed by afip? */
    public const CIERRE_Z = 58;
    /** @deprecated was changed by afip? */
    public const REMITO_R = 69;
    /** @deprecated was changed by afip? */
    public const PRESUPUESTO = 81;
    /** @deprecated was changed by afip? */
    public const REMITO_STOCK = 82;
    /** @deprecated was changed by afip? */
    public const DOCUMENT_NO_FISCAL = 83;
    /** @deprecated was changed by afip? */
    public const FACTURA_DE_EXPORTACION = 17;
}
