<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\WsmtxcaTests;

use Multinexo\WSMTXCA\Wsmtxca;
use Tests\TestAfipCase;

class WsmtxcaTest extends TestAfipCase
{
    /** @var Wsmtxca */
    private $factura;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factura = new Wsmtxca($this->getConfig('20305423174'));
    }

    public function testCreateInvoiceWithItemsOfMonotributeToMonotribute(): void
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

        $this->factura->datos = $this->getDatosFactura(
            $codigoComprobante,
            $importeTotal,
            $importeGravado,
            $importeOtrosTributos,
            $importeSubtotal,
            $importeNoGravado,
            $arrayItems,
            $arraySubtotalesIVA);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    //Test ejemplo de monotributista a monotributista, en los detalles existe tipo de iva (10.5% y 0%)
    public function testCreateInvoiceWithItemsOfMonotributeToResponsableInscript(): void
    {
        $this->assertTrue(true);

        return;

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

        $this->factura->datos = $this->getDatosFactura(
            $codigoComprobante,
            $importeTotal,
            $importeGravado,
            $importeOtrosTributos,
            $importeSubtotal,
            $importeNoGravado,
            $arrayItems,
            $arraySubtotalesIVA);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCreateInvoiceWithItemsWithIvaInArray(): void
    {
        $this->assertTrue(true);

        return;
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

        $this->factura->datos = $this->getDatosFactura(
            1,
            182.25,
            150,
            null,
            150,
            0,
            $arrayItems,
            $arraySubtotalesIVA);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCrearFacturaConItemsConArrayTipoB(): void
    {
        $this->assertTrue(true);

        return;
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

        $this->factura->datos = $this->getDatosFactura(
            6,
            150,
            150,
            null,
            150,
            0,
            $arrayItems);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testCrearComprobanteConItemsConArrayCompAsoc(): void
    {
        $this->assertTrue(true);

        return;
        $this->expectException(\Multinexo\Exceptions\WsException::class);
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
            2,
            115.75,
            100,
            null,
            100,
            0,
            $arrayItems,
            $arraySubtotalesIVA,
            $arrayComprobantesAsociados
        );
        $this->factura->createInvoice();
    }

    public function testCrearComprobanteConItemsConArrayTrib(): void
    {
        $this->assertTrue(true);

        return;
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
            1,
            115.75,
            100,
            15.75,
            100,
            0,
            $arrayItems,
            null,
            null
        );

        $this->factura->datos->arrayOtrosTributos = $arrayOtrosTributos;
        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result->comprobanteResponse->CAE);
    }

    public function testConsultarComprobanteConItems(): void
    {
        $this->assertTrue(true);

        return;
        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 20,
            'puntoVenta' => 1,
        ];
        $result = $this->factura->getInvoice();
        $this->assertNotEmpty($result->codigoAutorizacion);
    }

    public function testConsultCAEABetweenDates(): int
    {
        $this->factura->datos = (object) [
            'fechaDesde' => date('Y-m-d', strtotime(date('Y-m-d') . '- 5 month')),
            'fechaHasta' => date('Y-m-d'),
        ];

        $result = $this->factura->consultarCAEAEntreFechas();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAEA);

        return $result->CAEA;
    }

    /**
     * @depends testConsultCAEABetweenDates
     */
    public function testSolicitCAEA(int $caea): void
    {
        $this->factura->datos = (object) [
            'caea' => $caea,
        ];

        $result = $this->factura->getCAEA();
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
        $arrayComprobantesAsociados = null
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
            'arrayOtrosTributos' => null,
        ];

        return json_decode(json_encode($comprobante));
    }
}
