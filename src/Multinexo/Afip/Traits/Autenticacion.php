<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\Traits;

use Multinexo\Afip\Autenticacion as claseAutenticacion;
use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\WSAA\Wsaa;

/**
 * Class Autenticacion.
 */
trait Autenticacion
{
    /**
     * Setea la configuracion, en caso de enviarse un array con datos de configuracion esta reemplaza a la definida por
     * defecto.
     *
     * @param array $newConf
     *
     * @return $this
     */
    public function setearConfiguracion($newConf = [])
    {
        $defConf = include __DIR__ . '/../../../config/config.php';
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
        $this->autenticacion = new claseAutenticacion();
        $this->autenticacion->configuracion = $this->configuracion;
        $this->client = $this->autenticacion->getClient($this->ws);
        $this->authRequest = $this->autenticacion->getCredentials($this->ws);

        return true;
    }
}
