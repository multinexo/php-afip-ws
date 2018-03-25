<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip;

use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\Traits\Autenticacion as TraitAutenticacion;
use Multinexo\Afip\Traits\General;

class Autenticacion
{
    use TraitAutenticacion, General;
    public $configuracion;

    /**
     * Autenticacion constructor.
     */
    public function __construct()
    {
    }

    public function getClient($ws)
    {
        $ta = $this->configuracion->dir->xml_generados . 'TA-' . $this->configuracion->cuit . '-' . $ws . '.xml';
        $wsdl = __DIR__ . '/' . strtoupper($ws) . '/' . $ws . '.wsdl';

        foreach ([$ta, $wsdl] as $item) {
            if (!file_exists($item)) {
                throw new WsException('Fallo al abrir: ' . $item);
            }
        }

        $client = $this->connectToSoapClient($wsdl, $this->configuracion->url->{$ws});

        return $client;
    }

    public function connectToSoapClient($wsdlPath, $url)
    {
        $client = new \SoapClient($wsdlPath,
            [
                'soap_version' => SOAP_1_2,
                'location' => $url,
                'exceptions' => 0,
                'trace' => 1,
            ]);

        return $client;
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
