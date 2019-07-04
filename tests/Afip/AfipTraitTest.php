<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Afip;

trait AfipTraitTest
{
    private function getConf(string $cuit = '30615459190'): array
    {
        $base_path = getcwd();
        $wsurl = 'https://awshomo.afip.gov.ar/padron-puc-ws/services/';

        return [
            'dir' => [
                'xml_generados' => $base_path . '/tests/resources/' . sha1($cuit) . '/xml_generated/',
            ],
            'archivos' => [
                'wsaaWsdl' => $base_path . '/src/WSAA/wsaa.wsdl',
                'certificado' => $base_path . '/tests/resources/certificate-testing.crt',
                'clavePrivada' => $base_path . '/tests/resources/privateKey',
            ],
            'cuit' => $cuit,
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
