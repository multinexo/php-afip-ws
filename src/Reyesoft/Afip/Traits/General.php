<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Reyesoft\Afip\Traits;

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
