<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use Multinexo\Exceptions\WsException;
use Multinexo\Traits\Validaciones;
use Multinexo\WSFE\WsParametros as WsfeParameters;
use Multinexo\WSMTXCA\WsParametros as WsmtxcaParameters;

class AfipWebService
{
    use Validaciones;

    protected $service;

    /**
     * @var \stdClass
     */
    public $client;

    /**
     * @var \stdClass
     */
    protected $authRequest;

    /**
     * @var \stdClass
     */
    protected $datos;

    /**
     * @var string
     */
    protected $ws;

    /**
     * @var \stdClass
     */
    protected $configuracion;

    public function __construct($ws)
    {
        $this->ws = $ws;
    }

    public function isWsOkOrFail(string $ws): bool
    {
        $serverStatus = $this->getServerStatus($ws, $this->service->client);

        switch ($ws) {
            case 'wsmtxca':
                return $serverStatus->appserver === 'OK'
                    && $serverStatus->authserver === 'OK'
                    && $serverStatus->dbserver === 'OK';
            case 'wsfe':
                return $serverStatus->AppServer === 'OK'
                    && $serverStatus->DbServer === 'OK'
                    && $serverStatus->AuthServer === 'OK';

            case 'wspn3':
                return $serverStatus->appserver === 'OK'
                    && $serverStatus->authserver === 'OK'
                    && $serverStatus->dbserver === 'OK';
            default:
                throw new WsException('Error en la verificación del servicio');
        }
    }

    public function getPosNumbers(): array
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $result = (new WsfeParameters())->FEParamGetPtosVenta($this->service->client, $this->service->authRequest);
            if (empty((array) $result['ResultGet'])) {
                return [];
            }

            if (\count($result['ResultGet']->PtoVenta) > 1) {
                $puntosVenta = $result['ResultGet']->PtoVenta;
            } else {
                $puntosVenta = $result['ResultGet'];
            }

            foreach ($puntosVenta as $puntoVenta) {
                $codigos[] = $puntoVenta->Nro;
            }
        } elseif ($this->service->ws == 'wsmtxca') {
            $result = (new WsmtxcaParameters())->consultarPuntosVenta($this->service->client, $this->service->authRequest);

            if (empty((array) $result->arrayPuntosVenta)) {
                return [];
            }

            foreach ($result->arrayPuntosVenta as $puntoVenta) {
                $codigos[] = $puntoVenta->numeroPuntoVenta;
            }
        }

        return $codigos;
    }

    public function getAvailableCAEPosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws == 'wsmtxca') {
            $codigos = (new WsmtxcaParameters())->consultarPuntosVentaCAE($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //  // Todo: arreglar
        //  $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //  $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }

    public function getAvailableCAEAPosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws == 'wsmtxca') {
            $codigos = (new WsmtxcaParameters())->consultarPuntosVentaCAEA($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //            // Todo: arreglar
        //            $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //            $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }
}
