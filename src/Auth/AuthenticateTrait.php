<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Auth;

use Multinexo\Exceptions\WsException;
use Multinexo\WSAA\Wsaa;

/**
 * Class Authentication.
 */
trait AuthenticateTrait
{
    /**
     * Setea la configuracion, en caso de enviarse un array con datos de configuracion esta reemplaza a la definida por
     * defecto.
     *
     * @return $this
     */
    public function setearConfiguracion(array $newConf = [])
    {
        $defConf = include __DIR__ . '/../../config/config.php';
        $conf = array_replace_recursive($defConf, $newConf);
        $this->configuracion = json_decode(json_encode($conf));

        return $this;
    }

    // TODO: analizar lo que devuelve

    /**
     * Realiza las funciones de autorizacion necesarios para trabajar con el ws.
     *
     * @throws WsException
     */
    public function getAutenticacion(): bool
    {
        $this->wsaa = new Wsaa();
        $this->wsaa->configuracion = $this->configuracion;
        $this->wsaa->checkTARenovation($this->ws);
        $this->autenticacion = new Authentication();
        $this->autenticacion->configuracion = $this->configuracion;
        $this->client = $this->autenticacion->getClient($this->ws);
        $this->authRequest = $this->autenticacion->getCredentials($this->ws);

        return true;
    }
}
