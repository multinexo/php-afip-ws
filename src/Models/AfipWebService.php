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
use Multinexo\WSFE\Wsfe;
use Multinexo\WSFE\WsParametros as WsfeParameters;
use Multinexo\WSMTXCA\Wsmtxca;
use Multinexo\WSMTXCA\WsParametros as WsmtxcaParameters;
use Multinexo\WSPN3\Wspn3;

class AfipWebService
{
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

    public function __construct(string $ws)
    {
        $this->ws = $ws;
    }

    public static function setConfig(AfipConfig $afipConfig = null): array
    {
        $newConf = [
            'cuit' => $afipConfig->cuit ?? null,
            'dir' => [
                'xml_generados' => $afipConfig->xml_generated_directory ?? null,
            ],
            'archivos' => [
                'certificado' => $afipConfig->certificate_path ?? null,
                'clavePrivada' => $afipConfig->privatekey_path ?? null,
            ],
        ];

        $defConf = include getcwd() . '/config/config.php';
        $conf = array_replace_recursive($defConf, $newConf);

        $mode_sandbox = $afipConfig->sandbox ?? false;
        if (!$mode_sandbox) {
            $conf['url'] = $defConf['url_production'];
        }
        unset($conf['url_production']);

        return $conf;
    }

    public function isWsOkOrFail(): bool
    {
        $serverStatus = $this->getServerStatus();

        switch ($this->ws) {
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

    private function getServerStatus(): \stdClass
    {
        $cliente = $this->service->client;

        switch ($this->ws) {
            case 'wsmtxca':
                return (new Wsmtxca())->Dummy($cliente);
            case 'wsfe':
                return (new Wsfe())->FEDummy($cliente);
            case 'wspn3':
                return (new Wspn3())->wsDummy($cliente);
            default:
                throw new WsException('Error en la verificación del servicio');
        }
    }

    public function getPosNumbers(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
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
        } elseif ($this->service->ws === 'wsmtxca') {
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
        if ($this->service->ws === 'wsmtxca') {
            $codigos = (new WsmtxcaParameters())->consultarPuntosVentaCAE($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws === 'wsfe') {
        //  // Todo: arreglar
        //  $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //  $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }

    public function getAvailableCAEAPosNumbers(): array
    {
        $codigos = [];
        if ($this->service->ws === 'wsmtxca') {
            $codigos = (new WsmtxcaParameters())->consultarPuntosVentaCAEA($this->service->client, $this->service->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws === 'wsfe') {
        //            // Todo: arreglar
        //            $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //            $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }
}
