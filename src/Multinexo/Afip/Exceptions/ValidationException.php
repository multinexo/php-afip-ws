<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
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
