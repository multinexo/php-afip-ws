<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\Exceptions;

class ValidationException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = json_encode($message);
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function setModel($message, $code = 404)
    {
        $this->code = $code;
        $this->message = $message;

        return $this;
    }
}
