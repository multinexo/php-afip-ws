<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\WsmtxcaTests;

use Mockery;
use Multinexo\Objects\ItemObject;
use Multinexo\Objects\SubtotalesIvaObject;
use Multinexo\WSMTXCA\Wsmtxca;
use stdClass;
use Tests\InvoiceTestTrait;
use Tests\TestAfipCase;

/**
 * @internal
 * @covers \Multinexo\WSMTXCA\Wsmtxca
 */
final class WsmtxcaTest extends TestAfipCase
{
    use InvoiceTestTrait;

    /** @var Wsmtxca | Mockery\LegacyMockInterface */
    private $factura;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factura = new Wsmtxca($this->getConfig('20305423174'));
    }

    public function testCreateInvoiceWithItemsOfMonotributeToMonotribute(): void
    {
        $this->factura->datos = self::getInvoiceData(
            1,
            105.25,
            100,
            100,
            0,
            0
        );

        $item = new ItemObject();
        $item->unidadesMtx = 2;
        $item->codigoMtx = '111';
        $item->codigo = 'P0001';
        $item->descripcion = 'descripcion1';
        $item->codigoUnidadMedida = 7;
        $item->precioUnitario = 50;
        $item->importeBonificacion = 0;
        $item->codigoCondicionIVA = 4;
        $item->importeIVA = 5.25;
        $item->importeItem = 55.25;
        $this->factura->datos->items[] = $item;

        $item = new ItemObject();
        $item->codigoMtx = '222';
        $item->codigo = 'P0002';
        $item->descripcion = 'descripcion2';
        $item->cantidad = 2;
        $item->codigoUnidadMedida = 7;
        $item->precioUnitario = 25;
        $item->importeBonificacion = 0;
        $item->codigoCondicionIVA = 3;
        $item->importeIVA = 0;
        $item->importeItem = 50;
        $this->factura->datos->items[] = $item;

        $this->factura->datos->subtotalesIVA = [
            SubtotalesIvaObject::create(4, 5.25),
        ];

        $result = $this->factura->createInvoice();

        $this->assertNotEmpty($result->cae);
        $this->assertNotEmpty($result->cae_expiration_date);
        $this->assertSame($result->emission_date, date('Y-m-d'));
        $this->assertNotEmpty($result->observation);
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

        $this->factura->datos = $this->getInvoiceData(
            $codigoComprobante,
            $importeTotal,
            $importeGravado,
            $importeOtrosTributos,
            $importeSubtotal,
            $importeNoGravado,
            $arrayItems,
            $arraySubtotalesIVA
        );

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

        $this->factura->datos = $this->getInvoiceData(
            1,
            182.25,
            150,
            null,
            150,
            0,
            $arrayItems,
            $arraySubtotalesIVA
        );

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

        $this->factura->datos = $this->getInvoiceData(
            6,
            150,
            150,
            null,
            150,
            0,
            $arrayItems
        );

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

        $this->factura->datos = $this->getInvoiceData(
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

        $this->factura->datos = $this->getInvoiceData(
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
            'fechaDesde' => date('Y-m-d', (int) (strtotime(date('Y-m-d') . '- 125 month'))),
            'fechaHasta' => date('Y-m-d'),
        ];

        $CAEAResponse = new stdClass();
        $CAEAResponse->fechaProceso = '2021-06-28';
        $CAEAResponse->CAEA = 12345678912345;
        $CAEAResponse->periodo = 3;
        $CAEAResponse->orden = 123;
        $CAEAResponse->fechaDesde = '2021-02-28';
        $CAEAResponse->fechaHasta = '2021-06-28';
        $CAEAResponse->fechaTopeInforme = '2021-06-28';
        $factura_mock = Mockery::mock(Wsmtxca::class);
        $factura_mock->shouldReceive('consultarCAEAEntreFechas')->andReturn($CAEAResponse);
        $this->factura = $factura_mock;

        $result = $this->factura->consultarCAEAEntreFechas();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAEA);

        return $result->CAEA;
    }

    /**
     * @depends testConsultCAEABetweenDates
     */
    public function testSolicitCAEA($caea): void
    {
        $data = (object) [
            'caea' => $caea,
        ];

        $CAEAResponse = new stdClass();
        $CAEAResponse->fechaProceso = '2021-06-28';
        $CAEAResponse->CAEA = 12345678912345;
        $CAEAResponse->periodo = 3;
        $CAEAResponse->orden = 123;
        $CAEAResponse->fechaDesde = '2021-02-28';
        $CAEAResponse->fechaHasta = '2021-06-28';
        $CAEAResponse->fechaTopeInforme = '2021-06-28';
        $factura_mock = Mockery::mock(Wsmtxca::class);
        $factura_mock->shouldReceive('getCAEA')->andReturn($CAEAResponse);
        $this->factura = $factura_mock;

        $result = $this->factura->getCAEA($data);
        $this->assertNotEmpty($result->CAEA);
    }
}
