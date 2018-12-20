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
use Multinexo\WSFE\Wsfe;
use Multinexo\WSFE\WsParametros as FeSinItemsParam;
use Multinexo\WSMTXCA\Wsmtxca;
use Multinexo\WSMTXCA\WsParametros as FeConItemsParam;

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

    public function __construct(string $ws, array $config = [])
    {
        $service = $this->getClass($ws);
        $service->setearConfiguracion($config);
        $service->getAutenticacion();
        $this->service = $service;
        $this->ws = $ws;
    }

    private function getClass(string $ws)
    {
        if ($ws === 'wsfe') {
            return new Wsfe();
        } elseif ($ws === 'wsmtxca') {
            return new Wsmtxca();
        } else {
            throw new \Exception('El Web Service de la AFIP no es válido.');
        }
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
        if ($this->service->ws == 'wsfe') {
            $result = (new FeSinItemsParam())->FEParamGetPtosVenta($this->service->client, $this->service->authRequest);
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
            $result = (new FeConItemsParam())->consultarPuntosVenta($this->service->client, $this->service->authRequest);

            if (empty((array) $result->arrayPuntosVenta)) {
                return [];
            }

            foreach ($result->arrayPuntosVenta as $puntoVenta) {
                $codigos[] = $puntoVenta->numeroPuntoVenta;
            }
        }

        return $codigos;
    }

    public function getAvailablePosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws == 'wsfe') {
            $result = (new FeSinItemsParam())->FEParamGetPtosVenta($this->service->client, $this->service->authRequest);
            if (empty((array) $result['ResultGet'])) {
                return [];
            }

            if (\count($result['ResultGet']->PtoVenta) > 1) {
                $puntosVenta = $result['ResultGet']->PtoVenta;
            } else {
                $puntosVenta = $result['ResultGet'];
            }

            foreach ($puntosVenta as $puntoVenta) {
                if ($puntoVenta->Bloqueado === 'N') {
                    $codigos[] = $puntoVenta->Nro;
                }
            }
        } elseif ($this->service->ws == 'wsmtxca') {
            $result = (new FeConItemsParam())->consultarPuntosVenta($this->service->client, $this->service->authRequest);

            if (empty((array) $result->arrayPuntosVenta)) {
                return [];
            }

            foreach ($result->arrayPuntosVenta as $puntoVenta) {
                if ($puntoVenta->bloqueado == 'No') {
                    $codigos[] = $puntoVenta->numeroPuntoVenta;
                }
            }
        }

        return $codigos;
    }

    public function getAvailableCAEPosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarPuntosVentaCAE($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //  // Todo: arreglar
        //  $codigos = (new FeSinItemsParam())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //  $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }

    public function getAvailableCAEAPosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarPuntosVentaCAEA($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //            // Todo: arreglar
        //            $codigos = (new FeSinItemsParam())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //            $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }
}
