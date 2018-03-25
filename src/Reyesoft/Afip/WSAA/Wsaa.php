<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Reyesoft\Afip\WSAA;

use Reyesoft\Afip\Exceptions\WsException;
use Reyesoft\Afip\Traits\General;

/**
 * Class wsaa
 * Based in Gerardo Fisanotti wsaa-client.php
 * Define: WSDL, CERT, PRIVATEKEY, PASSPHRASE, SERVICE, WSAAURL
 * Returns: TA.xml (WSAA authorization ticket).
 */
class Wsaa
{
    use General;
    /**
     * @var
     */
    public $configuracion;

    /**
     * Crea un pedido de ticket de acceso (Access Request Ticket (TRA)).
     *
     * @param $service : Receive the service name (wsfe, wsbfe, wsfex, wsctg, etc.)
     */
    public function createTRA($service): void
    {
        $TRA = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '</loginTicketRequest>');
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', date('U'));
        $TRA->header->addChild('generationTime', date('c', date('U') - 60));
        $TRA->header->addChild('expirationTime', date('c', date('U') + 60));
        $TRA->addChild('service', $this->getOriginalWsName($service));
        $TRA->asXML($this->configuracion->dir->xml_generados . 'TRA-' . $service . '.xml');
    }

    /**
     * Crea la firma PKCS#7 usando entrada el archivo TRA*.xml (Pedido de Ticket de Autorización), el certificado  y
     * la clave privada. Genera un archivo intermedio y finalmente ajusta la cabecera MIME dejando el CMS final
     * requerido por WSAA.
     *
     * @param $service
     *
     * @return string
     *
     * @throws WsException
     */
    public function signTRA($service)
    {
        $configuracion = $this->configuracion;
        $dir = $configuracion->dir;
        $archivos = $configuracion->archivos;

        $STATUS = openssl_pkcs7_sign(
            $dir->xml_generados . 'TRA-' . $service . '.xml',
            $dir->xml_generados . 'TRA-' . $service . '.tmp',
            'file://' . $archivos->certificado,
            [
                'file://' . $archivos->clavePrivada,
                $configuracion->passPhrase,
            ],
            [],
            !PKCS7_DETACHED
        );
        if (!$STATUS) {
            throw new WsException('Error en la generacion de la firma PKCS#7');
        }
        $inf = fopen($dir->xml_generados . 'TRA-' . $service . '.tmp', 'r');
        $i = 0;
        $CMS = '';

        while (!feof($inf)) {
            $buffer = fgets($inf);
            if ($i++ >= 4) {
                $CMS .= $buffer;
            }
        }
        fclose($inf);
        //  unlink("TRA.xml");
        unlink($dir->xml_generados . 'TRA-' . $service . '.tmp');

        return $CMS;
    }

    /**
     * Conecta con el servidor remoto y ejecuta el metodo remoto LoginCMS retornando un Ticket de Acceso (TA).
     *
     * @param $CMS : Recibe un CMS (Cryptographic Message Syntax)
     *
     * @return mixed: Ticket de Acceso generado por AFIP en formato xml
     *
     * @throws WsException
     */
    public function callWSAA($CMS)
    {
        $client = new \SoapClient($this->configuracion->archivos->wsaaWsdl, [
            'proxy_port' => $this->configuracion->proxyPort,
            'soap_version' => SOAP_1_2,
            'location' => $this->configuracion->url->wsaa,
            'trace' => 1,
            'exceptions' => 0,
        ]);
        $results = $client->loginCms(['in0' => $CMS]);
        file_put_contents($this->configuracion->dir->xml_generados . 'request-loginCms.xml', $client->__getLastRequest());
        file_put_contents($this->configuracion->dir->xml_generados . 'response-loginCms.xml', $client->__getLastResponse());
        if (is_soap_fault($results)) {
            throw new WsException('SOAP Fault: [' . $results->faultcode . ']: ' . $results->faultstring);
        }

        return $results->loginCmsReturn;
    }

    /**
     * Permite autenticarse al ws.
     *
     * @param $service
     *
     * @throws WsException
     */
    public function authenticate($service)
    {
        //        ini_set("soap.wsdl_cache_enabled", "0");
        $dir = $this->configuracion->dir;
        $archivos = $this->configuracion->archivos;

        /* Se crean los directorios en donde se alojaran las claves y los xmls generados en caso que no existan */
        foreach ($dir as $directory) {
            !is_dir($directory) ? mkdir($directory, 0777, true) : false;
        }

        /* Se verifica que exista la clave privada, el certificado y el wsaa.wsdl */
        foreach ($archivos as $archivo) {
            if (!file_exists($archivo)) {
                throw new WsException('Error al abrir el archivo "' . basename($archivo) . PHP_EOL . '", verifique su existencia');
            }
        }

        $this->createTRA($service);
        $CMS = $this->signTRA($service);
        $TA = $this->callWSAA($CMS);

        if (!file_put_contents($dir->xml_generados . 'TA-' . $this->configuracion->cuit . '-' . $service . '.xml', $TA)) {
            throw new WsException('Hubo un error al tratar de autenticarse');
        }

        return true;
    }

    /**
     * Permite obtener un atributo de un archivo con formato xml.
     *
     * @param       $path
     * @param array $nodes
     *
     * @return bool|\SimpleXMLElement|\SimpleXMLElement[]
     */
    public function getXmlAttribute($path, $nodes = [])
    {
        $TaXml = simplexml_load_file($path);
        foreach ($nodes as $node) {
            if (isset($TaXml->{$node})) {
                $TaXml = $TaXml->{$node};
            } else {
                return false;
            }
        }

        return $TaXml;
    }

    /**
     * Chequea si necesita renovar el Ticket de Acceso para el ws.
     *
     * @param $service
     *
     * @return bool
     *
     * @throws WsException
     */
    public function checkTARenovation($service)
    {
        $path = $this->configuracion->dir->xml_generados . 'TA-' . $this->configuracion->cuit . '-' . $service . '.xml';

        if (!file_exists($path)) {
            $this->authenticate($service);
        }

        $expirationTime = $this->getXmlAttribute($path, ['header', 'expirationTime']);

        if (strtotime($expirationTime) < strtotime(date('Y-m-d h:m:i'))) {
            $this->authenticate($service);

            return true;
        }

        return false;
    }
}
