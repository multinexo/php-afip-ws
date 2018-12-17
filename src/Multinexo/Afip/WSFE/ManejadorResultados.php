<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\WSFE;

use Multinexo\Afip\Exceptions\WsException;

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
        return isset($resultado->Events) ? $resultado->Events : null;
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
     * @param \stdClass $resultado
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
