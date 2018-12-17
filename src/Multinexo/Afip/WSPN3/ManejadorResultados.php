<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\WSPN3;

use Multinexo\Afip\Exceptions\WsException;

/**
 * Class ManejadorResultados.
 */
class ManejadorResultados
{
    /**
     * Recupera información de errores detectados lanzandolo en una excepción.
     *
     * @param \Exception $resultado
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
     * @param \stdClass $resultado
     */
    public function obtenerEventos($resultado)
    {
        return isset($resultado->evento) ? $resultado->evento : null;
    }

    /**
     * Recupera detalle de observaciones del comprobante.
     *
     * @param string $path
     * @param string $name
     */
    public function obtenerObservaciones($path, $name)
    {
        return $path->{$name} ?? null;
    }
}
