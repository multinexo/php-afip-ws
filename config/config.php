<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Directorio del archivo wsdl del WS de Autenticación y Autorización
    |--------------------------------------------------------------------------
    |
    | El WS de Autenticación y Autorización es un servicio B2B (“Business to Business”) que permite que los
    | computadoras pertenecientes a la AFIP y entes externos a la AFIP intercambien información en forma directa
    | sin intervención de operadores.
    |
    */

    'dir' => [
        'xml_generados' => null,
    ],

    'archivos' => [
        'wsaaWsdl' => getcwd() . '/src/WSAA/wsaa.wsdl',
        'certificado' => null,
        'clavePrivada' => null,
    ],

    'passPhrase' => null,

    'proxyHost' => '190.122.183.81',

    'proxyPort' => '80',

    'url' => [
        'wsaa' => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
        'wsmtxca' => 'https://fwshomo.afip.gov.ar/wsmtxca/services/MTXCAService',
        'wsfe' => 'http://wswhomo.afip.gov.ar/wsfev1/service.asmx',
        'wspn3' => 'https://awshomo.afip.gov.ar/padron-puc-ws/services/select.ContribuyenteNivel3SelectServiceImpl',
        'padron-puc-ws-consulta-nivel4' => 'https://awshomo.afip.gov.ar/padron-puc-ws/services/select.ContribuyenteNivel4SelectServiceImpl',
    ],
    'url_production' => [
        'wsaa' => 'https://wsaa.afip.gov.ar/ws/services/LoginCms',
        'wsmtxca' => 'https://serviciosjava.afip.gob.ar/wsmtxca/services/MTXCAService',
        'wsfe' => 'https://servicios1.afip.gov.ar/wsfev1/service.asmx',
        'wspn3' => 'https://aws.afip.gov.ar/padron-puc-ws/services/select.ContribuyenteNivel3SelectServiceImpl',
    ],

    'cuit' => null,
];
