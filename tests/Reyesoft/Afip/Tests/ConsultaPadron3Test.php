<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip\Tests;

use Multinexo\Afip\WSPN3\Wspn3;

class ConsultaPadron3Test extends \PHPUnit\Framework\TestCase
{
    public $wspn3;

    /**
     * FacturaSinItemsTest constructor.
     */
    public function __construct()
    {
        $this->wspn3 = new Wspn3();
        $this->wspn3->setearConfiguracion($this->getConf());
    }

    public function test_consultar_datos_persona(): void
    {
        $cuitContribuyente = '30561785402';
        $result = $this->wspn3->consultarDatosPersona($cuitContribuyente);

        $this->assertNotEmpty($result);
    }

    public function getConf()
    {
        $filename = '7320828c9153b2a9848d6bc45d3544236b22fc48';
        $wsurl = 'https://awshomo.afip.gov.ar/padron-puc-ws/services/';
        $config = [
            'dir' => [
                    // @todo fix path
                    'xml_generados' => '/storage/Afip/' . $filename . '/xml_generated/',
                ],
            'archivos' => [
                    'wsaaWsdl' => 'reyesoft/php-afip-ws/src/Reyesoft/Afip/WSAA/wsaa.wsdl',
                    'certificado' => 'storage/Afip/' . $filename . '/' . $filename . '.crt',
                    'clavePrivada' => 'storage/Afip/privateKey',
                ],
            'cuit' => '20327936221',
            'passPhrase' => '12345678',
            'proxyHost' => '10.20.152.113',
            'proxyPort' => '80',
            'url' => [
                    'wsaa' => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
                    'wsmtxca' => 'https://fwshomo.afip.gov.ar/wsmtxca/services/MTXCAService',
                    'wsfe' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
                    'wspn3' => $wsurl . 'select.ContribuyenteNivel3SelectServiceImpl',
                    'padron-puc-ws-consulta-nivel4' => $wsurl . 'select.ContribuyenteNivel4SelectServiceImpl',
                ],
        ];

        return $config;
    }
}
