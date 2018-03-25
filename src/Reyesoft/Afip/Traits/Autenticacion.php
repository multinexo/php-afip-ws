<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Reyesoft\Afip\Traits;

use Reyesoft\Afip\Autenticacion as claseAutenticacion;
use Reyesoft\Afip\Exceptions\WsException;
use Reyesoft\Afip\WSAA\Wsaa;

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
     * @return bool
     *
     * @throws WsException
     */
    public function getAutenticacion()
    {
        $this->wsaa = new Wsaa();
        $this->wsaa->configuracion = $this->configuracion;
        $this->wsaa->checkTARenovation($this->ws);
        $this->autenticacion = new claseAutenticacion();
        $this->autenticacion->configuracion = $this->configuracion;
        $this->client = $this->autenticacion->getClient($this->ws);
        $this->authRequest = $this->autenticacion->getCredentials($this->ws);
        if (!$this->checkServerStatus($this->ws, $this->client)) { // TODO: analizar si es conveniente dejarlo.
            throw new WsException('Web service `' . $this->ws . '` temporalmente no disponible');
        }

        return true;
    }
}
