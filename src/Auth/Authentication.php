<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Auth;

use Multinexo\Exceptions\WsException;
use Multinexo\Models\AfipConfig;
use Multinexo\Models\AfipWebService;
use Multinexo\WSAA\Wsaa;
use SoapClient;
use stdClass;

class Authentication
{
    /** @var stdClass|null */
    private $service;

    private $configuracion;

    public $authRequest;

    public $client;

    /**
     * Authentication constructor.
     */
    public function __construct(AfipConfig $newConf, string $ws)
    {
        $conf = AfipWebService::setConfig($newConf);
        $this->configuracion = json_decode(json_encode($conf));

        $this->auth($ws);
    }

    public function auth(string $ws): void
    {
        $this->service = new stdClass();

        try {
            $this->service->ws = $ws;
            $this->service->configuracion = $this->configuracion;
            (new Wsaa())->checkTARenovation($this->service);
            $this->client = $this->getClient();
            $this->authRequest = $this->getCredentials();
            $this->service->client = $this->client;
            AfipWebService::checkWsStatusOrFail($this->service->ws, $this->client);
            $this->service = null;
        } catch (WsException $exception) {
            throw new WsException('Error de autenticación: ' . $exception->getMessage());
        }
    }

    public function getClient()
    {
        $ta = $this->service->configuracion->dir->xml_generados . 'TA-' . $this->service->configuracion->cuit
            . '-' . $this->service->ws . '.xml';
        $wsdl = dirname(__DIR__) . '/' . strtoupper($this->service->ws) . '/' . $this->service->ws . '.wsdl';

        foreach ([$ta, $wsdl] as $item) {
            if (!file_exists($item)) {
                throw new WsException('Fallo al abrir: ' . $item);
            }
        }

        return $this->connectToSoapClient($wsdl, $this->service->configuracion->url->{$this->service->ws});
    }

    public function connectToSoapClient(string $wsdlPath, string $url)
    {
        return new SoapClient(
            $wsdlPath,
            [
                'soap_version' => SOAP_1_2,
                'location' => $url,
                'exceptions' => 0,
                'trace' => 1,
            ]
        );
    }

    public function getCredentials()
    {
        $ta = $this->service->configuracion->dir->xml_generados . 'TA-' . $this->service->configuracion->cuit
            . '-' . $this->service->ws . '.xml';
        $TA = simplexml_load_file($ta);
        if ($TA === false) {
            return '';
        }
        $token = $TA->credentials->token;
        $sign = $TA->credentials->sign;
        $authRequest = '';
        if ($this->service->ws === 'wsmtxca') {
            $authRequest = [
                'token' => $token,
                'sign' => $sign,
                'cuitRepresentada' => $this->service->configuracion->cuit,
            ];
        } elseif ($this->service->ws === 'wsfe') {
            $authRequest = [
                'Token' => $token,
                'Sign' => $sign,
                'Cuit' => $this->service->configuracion->cuit,
            ];
        } elseif ($this->service->ws === 'wspn3') {
            $authRequest = new stdClass();
            $authRequest->token = (string) $token;
            $authRequest->sign = (string) $sign;
        }

        return $authRequest;
    }
}
