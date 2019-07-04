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
use Multinexo\Traits\General;
use Multinexo\WSAA\Wsaa;

class Authentication
{
    use AuthenticateTrait, General;

    public $configuracion;

    /** @var Wsaa */
    protected $wsaa;

    /** @var string */
    protected $ws;

    /** @var self */
    protected $autenticacion;

    /** @var \stdClass */
    protected $authRequest;

    /** @var \stdClass */
    protected $client;

    /**
     * Authentication constructor.
     */
    public function __construct()
    {
    }

    public function getClient($ws)
    {
        $ta = $this->configuracion->dir->xml_generados . 'TA-' . $this->configuracion->cuit . '-' . $ws . '.xml';
        $wsdl = dirname(__DIR__) . '/' . strtoupper($ws) . '/' . $ws . '.wsdl';

        foreach ([$ta, $wsdl] as $item) {
            if (!file_exists($item)) {
                throw new WsException('Fallo al abrir: ' . $item);
            }
        }

        return $this->connectToSoapClient($wsdl, $this->configuracion->url->{$ws});
    }

    public function connectToSoapClient($wsdlPath, $url)
    {
        return new \SoapClient($wsdlPath,
            [
                'soap_version' => SOAP_1_2,
                'location' => $url,
                'exceptions' => 0,
                'trace' => 1,
            ]);
    }

    public function getCredentials($ws)
    {
        $ta = $this->configuracion->dir->xml_generados . 'TA-' . $this->configuracion->cuit . '-' . $ws . '.xml';
        $TA = simplexml_load_file($ta);
        $token = $TA->credentials->token;
        $sign = $TA->credentials->sign;
        $authRequest = '';
        if ($ws == 'wsmtxca') {
            $authRequest = [
                'token' => $token,
                'sign' => $sign,
                'cuitRepresentada' => $this->configuracion->cuit,
            ];
        } elseif ($ws == 'wsfe') {
            $authRequest = [
                'Token' => $token,
                'Sign' => $sign,
                'Cuit' => $this->configuracion->cuit,
            ];
        } elseif ($ws == 'wspn3') {
            $authRequest = new \stdClass();
            $authRequest->token = (string) $token;
            $authRequest->sign = (string) $sign;
        }

        return $authRequest;
    }
}
