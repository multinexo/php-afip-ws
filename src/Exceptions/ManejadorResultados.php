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

    /**
     * @param \SoapFault|stdClass $resultado
     *
     * @throws WsException
     */
    public function procesar($resultado): void
    {
        $errores = '';
        if (isset($resultado->Errors)) {
            $errores = reset($resultado->Errors)->Msg;
        } elseif (isset($resultado->arrayErrores)) {
            if (isset($resultado->arrayErrores->codigoDescripcion->descripcion)) {
                $resultado->arrayErrores->codigoDescripcion = [$resultado->arrayErrores->codigoDescripcion];
            }
            foreach ($resultado->arrayErrores->codigoDescripcion as $err) {
                $errores .= $err->descripcion . ' (' . $err->codigo . ') ';
            }
        } elseif ($resultado instanceof \SoapFault) {
            $errores = $resultado->getMessage();
        }

        if (empty($errores)) {
            return;
        }

        throw new WsException($errores);
    }
}
