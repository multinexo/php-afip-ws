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
use Multinexo\WSMTXCA\Wsmtxca;
use Multinexo\WSMTXCA\WsParametros as WsmtxcaParameters;
use Multinexo\WSPN3\Wspn3;

class AfipWebService
{
    private $service;

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

        $defConf = include __DIR__ . '/../../config/config.php';
        $conf = array_replace_recursive($defConf, $newConf);

        $mode_sandbox = $afipConfig->sandbox ?? false;
        if (!$mode_sandbox) {
            $conf['url'] = $defConf['url_production'];
        }
        unset($conf['url_production']);

        return $conf;
    }

    public static function checkWsStatusOrFail(string $ws, \SoapClient $client): void
    {
        switch ($ws) {
            case 'wsmtxca':
                $serverStatus = Wsmtxca::dummy($client);

                $status = $serverStatus->appserver === 'OK'
                    && $serverStatus->authserver === 'OK'
                    && $serverStatus->dbserver === 'OK';
                break;
            case 'wspn3':
                $serverStatus = Wspn3::dummy($client);

                $status = $serverStatus->appserver === 'OK'
                    && $serverStatus->authserver === 'OK'
                    && $serverStatus->dbserver === 'OK';
                break;
            case 'wsfe':
            default:
                $serverStatus = Wsfe::dummy($client);

                $status = $serverStatus->AppServer === 'OK'
                    && $serverStatus->DbServer === 'OK'
                    && $serverStatus->AuthServer === 'OK';
                break;
        }

        if (!$status) {
            throw new WsException('Web service `' . $ws . '` temporalmente no disponible.');
        }
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
        //  $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->service->client, $this->authRequest);
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
        //            $codigos = (new WsfeParameters())->FEParamGetPtosVenta($this->service->client, $this->authRequest);
        //            $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }
}
