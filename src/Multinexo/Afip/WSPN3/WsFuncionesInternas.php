<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip\WSPN3;

use Multinexo\Afip\Exceptions\WsException;

class WsFuncionesInternas
{
    /**
     * @var
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
        $resultado = json_decode(json_encode($resultado));

        return $resultado;
    }

    public function wsDummy($client)
    {
        $resultado = $client->dummy();

        if (is_soap_fault($resultado)) {
            throw new WsException($resultado->getMessage(), 500);
        }

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte
        $resultado = json_decode(json_encode($resultado));

        return $resultado;
    }
}
