<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\WsfeTests;

use Multinexo\Exceptions\ValidationException;
use Multinexo\Exceptions\WsException;
use Multinexo\Objects\AssociatedDocumentObject;
use Multinexo\Objects\InvoiceObject;
use Multinexo\Objects\SubtotalesIvaObject;
use Multinexo\WSFE\Wsfe;
use stdClass;
use Tests\InvoiceTestTrait;
use Tests\TestAfipCase;

/**
 * @internal
 * @covers \Multinexo\WSFE\Wsfe
 */
final class WsfeTest extends TestAfipCase
{
    use InvoiceTestTrait;

    /** @var Wsfe */
    private $factura;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factura = new Wsfe($this->getConfig('20305423174'));
    }

    public function testCreateInvoiceWithoutItems(): void
    {
        $this->factura->datos = self::getInvoiceData(
            1,
            200.00,
            0,
            0,
            200.00,
            0
        );

        $result = $this->factura->createInvoice();

        $this->assertNotEmpty($result->cae);
        $this->assertNotEmpty($result->cae_expiration_date);
        $this->assertSame($result->emission_date, date('Y-m-d'));
        $this->assertNotEmpty($result->observation);
    }

    public function testCreateInvoiceWithoutItemsMonotributo(): void
    {
        $this->factura->datos = self::getInvoiceData(
            11,
            200.00,
            200,
            0,
            0.00,
            0
        );

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->cae);
    }

    public function testCreateInvoiceWithoutItemsWithReceiptsAssociated(): void
    {
        $arrayComprobantesAsociados = [
            AssociatedDocumentObject::create(2, 1221, 123131),
            AssociatedDocumentObject::create(3, 1223, 4353),
        ];
        $this->factura->datos = self::getInvoiceData(
            2,
            200.00,
            0,
            0,
            200.00
        );
        $this->factura->datos->comprobantesAsociados = $arrayComprobantesAsociados;

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->cae);
    }

    public function testCreateInvoiceWithoutItemsMonotributoWithTributes(): void
    {
        $arrayOtrosTributos = [
            (object) [
                'codigoTributo' => 2,
                'descripcion' => 'descripcion',
                'baseImponible' => 123131,
                'alicuota' => 1,
                'importe' => 5.00,
            ],
            (object) [
                'codigoTributo' => 1,
                'descripcion' => 'descripcion',
                'baseImponible' => 123.45,
                'alicuota' => 1,
                'importe' => 5.00,
            ],
        ];

        $this->factura->datos = self::getInvoiceData(
            11,
            20.00,
            10,
            10,
            0,
            0,
            10
        );
        $this->factura->datos->arrayOtrosTributos = $arrayOtrosTributos;

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->cae);
    }

    public function testCreateInvoiceWithoutItemsWithIva(): int
    {
        $this->factura->datos = self::getInvoiceData(
            1,
            176.25,
            150,
            0,
            0,
            26.25
        );
        $this->factura->datos->subtotalesIVA = [
            SubtotalesIvaObject::create(5, 21, 100),
            SubtotalesIvaObject::create(4, 5.25, 50),
        ];

        $result = $this->factura->createInvoice();
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->cae);

        return $result->number;
    }

    /*
    public function testCreateInvoiceWithoutItemsWithOptionalData(): void
    {
        $this->expectException(WsException::class);
        self::expectExceptionMessageMatches('/El numero de proyecto ingresado \\d+ no es valido para el emisor \\d+/');

        $this->factura->datos = self::getInvoiceData(
            1,
            200.00,
            0,
            0,
            200.00,
            0
        );
        $this->factura->datos->opcionales = [
            (object) [
                'codigoOpcional' => 2,
                'valor' => '1',
            ],
            (object) [
                'codigoOpcional' => 91,
                'valor' => '89',
            ],
        ];
        $this->factura->createInvoice();
    }
    */

    public function testCreateInvoiceWithoutItemsWithErrorServer(): void
    {
        $this->expectException(WsException::class);

        $this->factura->datos = self::getInvoiceData(
            1,
            220.00,
            0,
            0,
            200.00,
            0
        );

        $this->factura->createInvoice();
    }

    public function testCreateInvoiceWithoutItemsWithValidationErrors(): void
    {
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
            $arrayComprobantesAsociados
        );

        $this->factura->createInvoice();
    }

    /**
     * @depends testCreateInvoiceWithoutItemsWithIva
     */
    public function testConsultInvoice(int $cbte_nro): void
    {
        $this->factura->datos = new InvoiceObject();
        $this->factura->datos->codigoComprobante = 1;
        $this->factura->datos->numeroComprobante = $cbte_nro;
        $this->factura->datos->puntoVenta = 3;

        $result = $this->factura->getInvoice();
        $this->assertNotEmpty($result);
    }

    public function testConsultInvoiceWithErrorServer(): void
    {
        $this->expectException(WsException::class);

        $this->factura->datos = new InvoiceObject();
        $this->factura->datos->codigoComprobante = 1;
        $this->factura->datos->numeroComprobante = 9999;
        $this->factura->datos->puntoVenta = 3;

        $this->factura->getInvoice();
    }

    public function testConsultInvoiceWithValidationError(): void
    {
        $this->expectException(ValidationException::class);

        $this->factura->datos = new InvoiceObject();
        $this->factura->datos->codigoComprobante = 1;
        $this->factura->datos->puntoVenta = 1;

        $this->factura->getInvoice();
    }

    public function testSolicitCAEA(): void
    {
        $datos = new stdClass();
        $datos->periodo = '201604';
        $datos->orden = 1;

        $result = $this->factura->getCAEA($datos);
        $this->assertNotEmpty($result);
    }

    public function testSolicitCAEAExpired(): void
    {
        $this->expectException(WsException::class);
        $this->expectExceptionMessage(
            'Fecha de envío podrá ser desde 5 días corridos anteriores al inicio hasta el último dia de cada quincena.'
            . ' Del 3/11/2016 hasta 3/31/2016'
        );

        $datos = new stdClass();
        $datos->periodo = '201603';
        $datos->orden = 2;

        $result = $this->factura->requestCAEA($datos);
        $this->assertNotEmpty($result);
    }
}
