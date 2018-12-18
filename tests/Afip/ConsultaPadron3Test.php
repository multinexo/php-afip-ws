<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Afip;

use Multinexo\WSPN3\Wspn3;

class ConsultaPadron3Test extends \PHPUnit\Framework\TestCase
{
    public $wspn3;

    /**
     * FacturaSinItemsTest constructor.
     */
    protected function setUp(): void
    {
        $this->wspn3 = new Wspn3();
        $this->wspn3->setearConfiguracion($this->getConf());
    }

    public function testConsultarDatosPersona(): void
    {
        $cuitContribuyente = '30561785402';
        $result = $this->wspn3->consultarDatosPersona($cuitContribuyente);
        $this->assertNotEmpty($result);
    }

    public function getConf()
    {
        $base_path = __DIR__ . '/../../../php-afip-ws';
        $filename = '7320828c9153b2a9848d6bc45d3544236b22fc48';
        $wsurl = 'https://awshomo.afip.gov.ar/padron-puc-ws/services/';

        return [
            'dir' => [
                // @todo fix path
                'xml_generados' => $base_path . '/storage/Afip/' . $filename . '/xml_generated/',
            ],
            'archivos' => [
                'wsaaWsdl' => $base_path . '/src/Multinexo/Afip/WSAA/wsaa.wsdl',
                'certificado' => $base_path . '/storage/Afip/' . $filename . '/' . $filename . '.crt',
                'clavePrivada' => $base_path . '/storage/Afip/privateKey',
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
    }
}
