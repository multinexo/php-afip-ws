<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Reyesoft\Afip\WSFE;

use Reyesoft\Afip\Exceptions\WsException;

/**
 * Class ManejadorResultados.
 */
class ManejadorResultados
{
    /**
     * Recupera información de eventos.
     *
     * @param $resultado
     *
     * @return array con eventos o null si no existen
     */
    public function obtenerEventos($resultado)
    {
        return isset($resultado->Events) ? $resultado->Events : null;
    }

    /**
     * Recupera detalle de observaciones del comprobante.
     *
     * @param $path
     * @param $name
     *
     * @return array con observaciones o null si no existen
     */
    public function obtenerObservaciones($path, $name)
    {
        return $path->{$name} ?? null;
    }

    /**
     * Recupera información de errores detectados lanzandolo en una excepción.
     *
     * @param $resultado
     *
     * @throws WsException
     */
    public function procesar($resultado): void
    {
        $errores = isset(reset($resultado)->Errors) ? reset($resultado)->Errors : null;
        if ($errores) {
            throw new WsException($errores);
        }
    }
}
