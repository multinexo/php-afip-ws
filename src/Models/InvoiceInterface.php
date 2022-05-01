<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use Multinexo\Objects\InvoiceResultObject;
use stdClass;

interface InvoiceInterface
{
    public function createInvoice(): InvoiceResultObject;

    public function getCAEA(stdClass $data): stdClass;

    public function requestCAEA(stdClass $datos): stdClass;

    public function getInvoice(): stdClass;
}
