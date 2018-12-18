<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Afip;

use Multinexo\Afip\Models\FacturaConItems;

class FacturaConItemsTest extends \PHPUnit\Framework\TestCase
{
    public $factura;

    /**
     * FacturaConItemsTest constructor.
     */
    protected function setUp(): void
    {
        $this->factura = new FacturaConItems();
        $this->factura->setearConfiguracion($this->getConf());
    }

    //Test ejemplo de monotributista a monotributista, en los detalles existe tipo de iva (10.5% y 0%)
    public function testMonotributistaMonotributista(): void
    {
        //@@@@@@@@@@@DETAILS
        //###CodigoCondicionIva
        //1 (No Gravado) | 2 (Exento) | 3 (0%) | 4 (10.50%) | 5 (21%) | 6 (27%)
        //Tabla taxes
        //###CodigoUnidadMedida
        //7 (unidad)
        //Tabla measures
        //@@@@@@@@@@DOCUMENT
        //###CodigoComprobante
        //1 (FACTURA A) | 6 (FACTURA B) | 11 (FACTURA C)

        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'descripcion1',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 4,
                    'importeIVA' => 5.25,
                    'importeItem' => 55.25,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    'importeIVA' => 0,
                    'importeItem' => 50,
                ],
            ],
        ];

        //Aca solamente son permitodos los tipos de IVA 4 - 5 - 6;
        $arraySubtotalesIVA = [
            'subtotalIVA' => [
                [
                    'codigoIva' => 4,
                    'importe' => 5.25,
                ],
            ],
        ];

        $codigoComprobante = 1;
        $importeTotal = 105.25;
        $importeGravado = 100;
        $importeOtrosTributos = null;
        $importeSubtotal = 100;
        $importeNoGravado = 0;

        //$arraySubtotalesIVA = $arraySubtotalesIVA;
        $arrayComprobantesAsociados = null;
        $arrayOtrosTributos = null;

        $this->factura->datos = $this->getDatosFactura(
            $codigoComprobante,
            $importeTotal,
            $importeGravado,
            $importeOtrosTributos,
            $importeSubtotal,
            $importeNoGravado,
            $arrayItems,
            $arraySubtotalesIVA,
            $arrayComprobantesAsociados,
            $arrayOtrosTributos);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    //Test ejemplo de monotributista a monotributista, en los detalles existe tipo de iva (10.5% y 0%)
    public function testsMonotributistaResponsableInscripto(): void
    {
        //@@@@@@@@@@@DETAILS
        //###CodigoCondicionIva
        //1 (No Gravado) | 2 (Exento) | 3 (0%) | 4 (10.50%) | 5 (21%) | 6 (27%)
        //Tabla taxes
        //###CodigoUnidadMedida
        //7 (unidad)
        //Tabla measures
        //@@@@@@@@@@DOCUMENT
        //###CodigoComprobante
        //1 (FACTURA A) | 6 (FACTURA B) | 11 (FACTURA C)

        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'descripcion1',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 4,
                    'importeIVA' => 5.25,
                    'importeItem' => 55.25,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    'importeIVA' => 0,
                    'importeItem' => 50,
                ],
            ],
        ];

        //Aca solamente son permitodos los tipos de IVA 4 - 5 - 6;
        $arraySubtotalesIVA = [
            'subtotalIVA' => [
                [
                    'codigoIva' => 4,
                    'importe' => 5.25,
                ],
            ],
        ];

        $codigoComprobante = 6;
        $importeTotal = 105.25;
        $importeGravado = 100;
        $importeOtrosTributos = null;
        $importeSubtotal = 100;
        $importeNoGravado = 0;

        //$arraySubtotalesIVA = $arraySubtotalesIVA;
        $arrayComprobantesAsociados = null;
        $arrayOtrosTributos = null;

        $this->factura->datos = $this->getDatosFactura(
            $codigoComprobante,
            $importeTotal,
            $importeGravado,
            $importeOtrosTributos,
            $importeSubtotal,
            $importeNoGravado,
            $arrayItems,
            $arraySubtotalesIVA,
            $arrayComprobantesAsociados,
            $arrayOtrosTributos);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCrearFacturaConItemsConArrayIva(): void
    {
        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'Descripción del producto P0001',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 100,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 6,
                    'importeIVA' => 27.00,
                    'importeItem' => 127.00,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 4,
                    'importeIVA' => 5.25,
                    'importeItem' => 55.25,
                ],
            ],
        ];
        $arraySubtotalesIVA = [
            'subtotalIVA' => [
                [
                    'codigoIva' => 6,
                    'importe' => 27.00,
                ],
                [
                    'codigoIva' => 4,
                    'importe' => 5.25,
                ],
            ],
        ];

        $this->factura->datos = $this->getDatosFactura(1, 182.25, 150, null, 150, 0, $arrayItems, $arraySubtotalesIVA);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCrearFacturaConItemsConArrayTipoB(): void
    {
        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'Descripción del producto P0001',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 100,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    //'importeIVA' => 27.00,
                    'importeItem' => 100.00,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    //'importeIVA' => 5.25,
                    'importeItem' => 50,
                ],
            ],
        ];

        $this->factura->datos = $this->getDatosFactura(6, 150, 150, null, 150, 0, $arrayItems);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCrearComprobanteConItemsConArrayCompAsoc(): void
    {
        $this->expectException(\Multinexo\Afip\Exceptions\WsException::class);
        $this->expectExceptionMessage('Para la CUIT, Tipo de Comprobante y Punto de Ventas requeridos ' .
            'no se registran comprobantes en las bases del Organismo');

        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'descripcion1',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 5,
                    'importeIVA' => 10.50,
                    'importeItem' => 60.50,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 4,
                    'importeIVA' => 5.25,
                    'importeItem' => 55.25,
                ],
            ],
        ];
        $arraySubtotalesIVA = [
            'subtotalIVA' => [
                [
                    'codigoIva' => 5,
                    'importe' => 10.50,
                ],
                [
                    'codigoIva' => 4,
                    'importe' => 5.25,
                ],
            ],
        ];
        $arrayComprobantesAsociados = [
            'comprobanteAsociado' => [
                [
                    'codigoComprobante' => 2,
                    'puntoVenta' => 1221,
                    'numeroComprobante' => 123131,
                ],
                [
                    'codigoComprobante' => 3,
                    'puntoVenta' => 1223,
                    'numeroComprobante' => 4353,
                ],
            ],
        ];

        $this->factura->datos = $this->getDatosFactura(
            2, 115.75, 100, null, 100, 0, $arrayItems, $arraySubtotalesIVA, $arrayComprobantesAsociados
        );
        $this->factura->crearComprobante();
    }

    public function testCrearComprobanteConItemsConArrayTrib(): void
    {
        $arrayItems = [
            'item' => [
                [
                    'unidadesMtx' => 2,
                    'codigoMtx' => '111',
                    'codigo' => 'P0001',
                    'descripcion' => 'descripcion1',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    'importeIVA' => 0,
                    'importeItem' => 50,
                ],
                [
                    'unidadesMtx' => 1,
                    'codigoMtx' => '222',
                    'codigo' => 'P0002',
                    'descripcion' => 'descripcion2',
                    'cantidad' => 1,
                    'codigoUnidadMedida' => 7,
                    'precioUnitario' => 50,
                    'importeBonificacion' => 0,
                    'codigoCondicionIVA' => 3,
                    'importeIVA' => 0,
                    'importeItem' => 50,
                ],
            ],
        ];

        $arrayOtrosTributos = [
            'otroTributo' => [
                [
                    'codigoComprobante' => 2,
                    'descripcion' => 'asdasd',
                    'baseImponible' => 10.50,
                    'importe' => 10.50,
                ],
                [
                    'codigoComprobante' => 3,
                    'descripcion' => 'asdasd',
                    'baseImponible' => 5.25,
                    'importe' => 5.25,
                ],
            ],
        ];

        $this->factura->datos = $this->getDatosFactura(
            1, 115.75, 100, 15.75, 100, 0, $arrayItems, null, null, $arrayOtrosTributos
        );

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testConsultarComprobanteConItems(): void
    {
        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 20,
            'puntoVenta' => 1,
        ];
        $result = $this->factura->consultarComprobante();
        $this->assertNotEmpty($result->codigoAutorizacion);
    }

    public function testConsultarCaeaEntreFechas(): void
    {
        $this->factura->datos = (object) [
            'fechaDesde' => '2015-05-30',
            'fechaHasta' => '2016-05-30',
        ];

        $result = $this->factura->consultarCAEAEntreFechas();
        $this->assertNotEmpty($result);
    }

    public function testSolicitarCaea(): void
    {
        $this->factura->datos = (object) [
            'periodo' => '201604',
            'orden' => 1,
        ];

        $result = $this->factura->solicitarCAEA();
        $this->assertNotEmpty($result);
    }

    public function testConsultarCaea(): void
    {
        $this->factura->datos = (object) [
            'caea' => 26119315562071,
        ];

        $result = $this->factura->consultarCAEA();
        $this->assertNotEmpty($result->CAEA);
    }

    public function getDatosFactura(
        $codigoComprobante,
        $importeTotal,
        $importeGravado,
        $importeOtrosTributos,
        $importeSubtotal,
        $importeNoGravado,
        $arrayItems,
        $arraySubtotalesIVA = null,
        $arrayComprobantesAsociados = null,
        $arrayOtrosTributos = null
    ) {
        $comprobante = [
            'cantidadRegistros' => 1,
            'puntoVenta' => 1,
            'codigoComprobante' => $codigoComprobante,
            'numeroComprobante' => null,
            'codigoConcepto' => 1,
            'codigoDocumento' => 80,
            'numeroDocumento' => 20305423174,
            'fechaEmision' => date('Y-m-d'),
            'codigoMoneda' => 'PES',
            'cotizacionMoneda' => 1,
            'importeGravado' => $importeGravado,
            'importeNoGravado' => $importeNoGravado,
            'importeExento' => 0,
            'importeOtrosTributos' => $importeOtrosTributos,
            'importeSubtotal' => $importeSubtotal,
            'importeIVA' => 0,
            'importeTotal' => $importeTotal,
            'codigoTipoAutorizacion' => null,
            'observaciones' => null,
            'fechaServicioDesde' => null,
            'fechaServicioHasta' => null,
            'fechaVencimientoPago' => null,
            'arrayItems' => $arrayItems,
            'arraySubtotalesIVA' => $arraySubtotalesIVA,
            'arrayComprobantesAsociados' => $arrayComprobantesAsociados,
            'arrayOtrosTributos' => $arrayOtrosTributos,
        ];

        return json_decode(json_encode($comprobante));
    }

    public function getConf()
    {
        // @todo
        $base_path = __DIR__ . '/../../../php-afip-ws';
        $dirAfip = $base_path . '/storage/Afip/4c15dc21c91634c1b301de6236eb08ead86be4ae';

        return [
            'dir' => [
                'xml_generados' => $dirAfip . '/xml_generated/',
            ],

            'archivos' => [
                'certificado' => $dirAfip . '/4c15dc21c91634c1b301de6236eb08ead86be4ae.crt',
                'clavePrivada' => $base_path . '/storage/Afip/privateKey',
            ],

            'cuit' => 20327936221,
        ];
    }
}
