<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use Multinexo\AfipWebServiceInterface;
use Multinexo\Auth\Authentication;
use Multinexo\Exceptions\ManejadorResultados;
use Multinexo\Objects\InvoiceObject;

abstract class InvoiceWebService implements InvoiceInterface, AfipWebServiceInterface
{
    /** @var Authentication */
    public $service;
    /** @var string */
    public $ws;
    /** @var InvoiceObject */
    public $datos;
    /** @var ManejadorResultados */
    public $resultado;

    public function __construct(AfipConfig $afipConfig)
    {
        $this->service = new Authentication($afipConfig, $this->ws);
    }
}
