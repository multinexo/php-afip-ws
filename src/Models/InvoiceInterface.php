<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

interface InvoiceInterface
{
    public function createInvoice();

    public function getCAEA();

    public function requestCAEA();

    public function getInvoice();
}
