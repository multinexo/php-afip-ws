<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Objects;

class InvoiceResultObject
{
    /** @var int|null Not available for Wsfe */
    public $pos_number;
    /** @var int */
    public $number;
    /** @var string */
    public $cae;
    /** @var string */
    public $emission_date;
    /** @var string */
    public $cae_expiration_date;
    /** @var string */
    public $observation = '';
    /** @var \stdClass */
    public $original_response;
}
