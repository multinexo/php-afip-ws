<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\Traits;

/**
 * Class General.
 */
trait General
{
    public function getAliasWsName($ws)
    {
        switch ($ws) {
            case 'padron-puc-ws-consulta-nivel3':
                $alias = 'wspn3';
                break;
            default:
                $alias = $ws;
        }

        return $alias;
    }

    public function getOriginalWsName($ws)
    {
        switch ($ws) {
            case 'wspn3':
                $original = 'padron-puc-ws-consulta-nivel3';
                break;
            default:
                $original = $ws;
        }

        return $original;
    }
}
