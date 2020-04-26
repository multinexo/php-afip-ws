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

abstract class Invoice implements InvoiceInterface, AfipWebServiceInterface
{
    public $service;

    public $ws;

    public $datos;

    public $resultado;

    public function __construct(AfipConfig $afipConfig)
    {
        $this->service = new Authentication($afipConfig, $this->ws);
    }
}
