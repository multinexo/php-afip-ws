<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use stdClass;

interface InvoiceInterface
{
    public function createInvoice(): stdClass;

    public function getCAEA(): stdClass;

    public function requestCAEA(): stdClass;

    public function getInvoice(): stdClass;
}
