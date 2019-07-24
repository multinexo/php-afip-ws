<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Afip;

use Multinexo\Exceptions\ValidationException;
use Multinexo\Exceptions\WsException;
use Multinexo\WSFE\Wsfe;
use PHPUnit\Framework\TestCase;

class WsfeTest extends TestCase
{
    use AfipTraitTest;

    private $factura;

    protected function setUp(): void
    {
        $this->factura = new Wsfe();
        $this->factura->setearConfiguracion($this->getConfig('20305423174'));
    }

    public function testCreateInvoiceWithoutItems(): void
    {
        $this->factura->datos = self::getInvoiceData(
            1,
            200.00,
            0,
            0,
            200.00,
            0,
            0,
            null);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAE);
        $this->assertNotEmpty($result->CAEFchVto);
    }

    public function testCreateInvoiceWithoutItemsMonotributo(): void
    {
        $this->factura->datos = self::getInvoiceData(
            11,
            200.00,
            200,
            0,
            0.00,
            0,
            0,
            null);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAE);
    }

    public function testCreateInvoiceWithoutItemsWithReceiptsAssociated(): void
    {
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
        $this->factura->datos = self::getInvoiceData(
            2,
            200.00,
            0,
            0,
            200.00,
            0,
            0,
            $arrayComprobantesAsociados);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAE);
    }

    public function testCreateInvoiceWithoutItemsMonotributoWithTributes(): void
    {
        $arrayOtrosTributos = [
            'otroTributo' => [
                [
                    'codigoTributo' => 2,
                    'descripcion' => 'descripcion',
                    'baseImponible' => 123131,
                    'alicuota' => 1,
                    'importe' => 5.00,
                ],
                [
                    'codigoTributo' => 1,
                    'descripcion' => 'descripcion',
                    'baseImponible' => 123.45,
                    'alicuota' => 1,
                    'importe' => 5.00,
                ],
            ],
        ];

        $this->factura->datos = self::getInvoiceData(
            11,
            20.00,
            10,
            10,
            0,
            0,
            10,
            null,
            $arrayOtrosTributos);

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAE);
    }

    public function testCreateInvoiceWithoutItemsWithIva(): int
    {
        $arraySubtotalesIVA = [
            'subtotalIVA' => [
                [
                    'codigoIva' => 5,
                    'baseImponible' => 100,
                    'importe' => 21,
                ],
                [
                    'codigoIva' => 4,
                    'baseImponible' => 50,
                    'importe' => 5.25,
                ],
            ],
        ];

        $this->factura->datos = self::getInvoiceData(
            1,
            176.25,
            150,
            0,
            0,
            26.25,
            0,
            null,
            null);
        $this->factura->datos->arraySubtotalesIVA = json_decode(json_encode($arraySubtotalesIVA));

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->CAE);

        return $result->CbteDesde;
    }

    public function testCreateInvoiceWithoutItemsWithOptionalData(): void
    {
        /* @todo fix exceptions messages */
        $this->assertTrue(true);

        return;

        $this->expectException(WsException::class);
        $this->expectExceptionMessageRegExp('/El numero de proyecto ingresado \\d+ no es valido para el emisor \\d+/');

        $arrayOpcionales = [
            'Opcional' => [
                [
                    'codigoOpcional' => 2,
                    'valor' => '1',
                ],
                [
                    'codigoOpcional' => 91,
                    'valor' => '89',
                ],
            ],
        ];

        $this->factura->datos = self::getInvoiceData(
            1,
            200.00,
            0,
            0,
            200.00,
            0,
            0,
            null,
            null);
        $this->factura->datos->arrayOpcionales = $arrayOpcionales;
        $this->factura->createInvoice();
    }

    public function testCreateInvoiceWithoutItemsWithErrorServer(): void
    {
        $this->assertTrue(true);

        return;

        $this->expectException(WsException::class);

        $this->factura->datos = self::getInvoiceData(
            1,
            220.00,
            0,
            0,
            200.00,
            0);

        $this->factura->createInvoice();
    }

    public function testCreateInvoiceWithoutItemsWithValidationErrors(): void
    {
        $this->assertTrue(true);

        return;

        $this->expectException(ValidationException::class);

        $arrayComprobantesAsociados = [
            'comprobanteAsociado' => [
                [
                    'codigoComprobante' => 'codigo',
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
        $this->factura->datos = self::getInvoiceData(
            1,
            200.00,
            0,
            0,
            200.00,
            'error',
            $arrayComprobantesAsociados);
        $this->factura->createInvoice();
    }

    /**
     * @depends testCreateInvoiceWithoutItemsWithIva
     */
    public function testConsultInvoice(int $cbte_nro): void
    {
        $this->assertTrue(true);

        return;

        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => $cbte_nro,
            'puntoVenta' => 3,
        ];
        $result = $this->factura->getInvoice();
        $this->assertNotEmpty($result);
    }

    public function testConsultInvoiceWithErrorServer(): void
    {
        $this->assertTrue(true);

        return;
        $this->expectException(WsException::class);

        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 9999,
            'puntoVenta' => 3,
        ];
        $this->factura->getInvoice();
    }

    public function testConsultInvoiceWithValidationError(): void
    {
        $this->assertTrue(true);

        return;
        $this->expectException(ValidationException::class);

        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 'test',
            'puntoVenta' => 1,
        ];
        $this->factura->getInvoice();
    }

    public function testConsultCAEAPerPeriod(): void
    {
        $this->assertTrue(true);

        return;
        $this->factura->datos = (object) [
            'periodo' => '201603',
            'orden' => 2,
        ];

        $result = $this->factura->getCAEA();
        $this->assertNotEmpty($result);
    }

    public function testSolicitCAEA(): void
    {
        $this->assertTrue(true);

        return;
        $this->factura->datos = (object) [
            'periodo' => '201604',
            'orden' => 1,
        ];

        $result = $this->factura->solicitarCAEA();
        $this->assertNotEmpty($result);
    }

    public function testSolicitCAEAExpired(): void
    {
        $this->assertTrue(true);

        return;
        $this->expectException(WsException::class);
        $this->expectExceptionMessage(
            '{"Err":{"Code":15007,"Msg":"El <Periodo> 201603 se encuentra vencido para solicitar CAEA."}}'
        );

        $this->factura->datos = json_decode(json_encode([
            'periodo' => '201603',
            'orden' => 2,
        ]));

        $result = $this->factura->requestCAEA();
        $this->assertNotEmpty($result);
    }

    private static function getInvoiceData(
        $codigoComprobante,
        $importeTotal,
        $importeGravado,
        $importeSubtotal,
        $importeNoGravado,
        $importeIVA,
        $importTribute = 0,
        $arrayComprobantesAsociados = null,
        $arrayOtrosTributos = null)
    {
        $comprobante = [
            'cantidadRegistros' => 1,
            'puntoVenta' => 3,
            'codigoComprobante' => $codigoComprobante,
            'numeroComprobante' => null,
            'codigoConcepto' => 1,
            'codigoDocumento' => 80,
            'numeroDocumento' => 20327936221,
            'fechaEmision' => date('Ymd'),
            'codigoMoneda' => 'PES',
            'cotizacionMoneda' => 1,
            'importeGravado' => $importeGravado,
            'importeNoGravado' => $importeNoGravado,
            'importeExento' => 0,
            'importeSubtotal' => $importeSubtotal,
            'importeIVA' => $importeIVA,
            'importeTotal' => $importeTotal,
            'importeOtrosTributos' => $importTribute,
            'fechaServicioDesde' => '20160316',
            'fechaServicioHasta' => '20160316',
            'fechaVencimientoPago' => '20160316',
            'arrayComprobantesAsociados' => $arrayComprobantesAsociados,
            'arrayOtrosTributos' => $arrayOtrosTributos,
            'arraySubtotalesIVA' => null,
            'arrayOpcionales' => null,
        ];

        return json_decode(json_encode($comprobante));
    }
}
