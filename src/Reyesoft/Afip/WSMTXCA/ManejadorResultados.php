<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip\WSMTXCA;

use Multinexo\Afip\Exceptions\WsException;

/**
 * Class ManejadorResultados.
 */
class ManejadorResultados
{
    /**
     * Recupera información de errores detectados lanzandolo en una excepción.
     *
     * @param $resultado
     *
     * @throws WsException
     */
    public function procesar($resultado): void
    {
        // cuando hay error $resultado->Resultado = 'R'

        $errores = null;
        //Porque el error viene de otra forma si existe message
        if (!property_exists($resultado, 'message')) {
            $errores = isset($resultado->arrayErrores) ?
                (isset($resultado->arrayErrores->codigoDescripcion) ?
                    $resultado->arrayErrores->codigoDescripcion
                    : $resultado->arrayErrores)
                : null;
        } else {
            $errores = $resultado->getMessage();
        }

        if ($errores) {
            throw new WsException($errores);
        }
    }

    /**
     * Recupera información de eventos.
     *
     * @param $resultado
     */
    public function obtenerEventos($resultado)
    {
        return isset($resultado->evento) ? $resultado->evento : null;
    }

    /**
     * Recupera detalle de observaciones del comprobante.
     *
     * @param $path
     * @param $name
     */
    public function obtenerObservaciones($path, $name)
    {
        return $path->{$name} ?? null;
    }
}
