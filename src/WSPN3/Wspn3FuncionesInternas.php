<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSPN3;

use Multinexo\Exceptions\WsException;
use Multinexo\WSMTXCA\ManejadorResultados;

trait Wspn3FuncionesInternas
{
    /**
     * @var ManejadorResultados
     */
    public $resultado;

    public function wsGet($client, $authRequest, $contribuyente)
    {
        $resultado = $client->get(
            $contribuyente,
            $authRequest->token,
            $authRequest->sign
        );

        $this->resultado->procesar($resultado);

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte
        return json_decode(json_encode($resultado));
    }

    public function wsDummy($client)
    {
        $resultado = $client->dummy();

        if (is_soap_fault($resultado)) {
            throw new WsException($resultado->getMessage(), 500);
        }

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte
        return json_decode(json_encode($resultado));
    }
}
