<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

class Log
{
    /** @var callable */
    public static $log_function;

    public static function setLogFunction(callable $log_function): void
    {
        self::$log_function = $log_function;
    }

    public static function debug(string $text): void
    {
        $fc = self::$log_function ?? function (string $text): void {
        };
        $fc($text);
    }
}
