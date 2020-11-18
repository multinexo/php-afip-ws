<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Exceptions;

use stdClass;

/**
 * Class ManejadorResultados.
 */
class ManejadorResultados
{
    // Recupera información de eventos.
    public function obtenerEventos(stdClass $resultado): ?stdClass
    {
        return $resultado->Events ?? $resultado->evento ?? null;
    }

    // Recupera detalle de observaciones del comprobante.
    public function obtenerObservaciones(stdClass $path, string $name): ?array
    {
        return $path->{$name} ?? null;
    }

    // Recupera información de errores detectados lanzandolo en una excepción.
    public function procesar(stdClass $resultado): void
    {
        if (isset($resultado->Errors)) {
            $errores = reset($resultado->Errors)->Msg;
        } else {
            //Porque el error viene de otra forma si existe message
            if (!property_exists($resultado, 'message')) {
                $errores = isset($resultado->arrayErrores) ?
                    (isset($resultado->arrayErrores->codigoDescripcion) ?
                        $resultado->arrayErrores->codigoDescripcion->descripcion
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
