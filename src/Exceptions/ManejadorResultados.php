<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Exceptions;

/**
 * Class ManejadorResultados.
 */
class ManejadorResultados
{
    /**
     * Recupera información de eventos.
     *
     * @param \stdClass $resultado
     *
     * @return \stdClass con eventos o null si no existen
     */
    public function obtenerEventos($resultado): \stdClass
    {
        return $resultado->Events ?? $resultado->evento ?? null;
    }

    /**
     * Recupera detalle de observaciones del comprobante.
     *
     * @param \stdClass $path
     * @param string $name
     *
     * @return array|null con observaciones o null si no existen
     */
    public function obtenerObservaciones($path, $name): ?array
    {
        return $path->{$name} ?? null;
    }

    /**
     * Recupera información de errores detectados lanzandolo en una excepción.
     *
     * @throws WsException
     */
    public function procesar($resultado): void
    {
        if (isset($resultado->Errors)) {
            $errores = reset($resultado->Errors);
        } else {
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
        }

        if (!empty($errores)) {
            throw new WsException($errores);
        }
    }
}
