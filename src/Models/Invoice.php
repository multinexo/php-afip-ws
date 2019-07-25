<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use Multinexo\Auth\AuthenticateTrait;

abstract class Invoice implements InvoiceInterface
{
    public $datos;

    public $client;

    public $resultado;

    protected $ws;

    protected $wsaa;

    protected $authRequest;

    protected $autenticacion;

    public $configuracion;

    use Validaciones, AuthenticateTrait;
}
