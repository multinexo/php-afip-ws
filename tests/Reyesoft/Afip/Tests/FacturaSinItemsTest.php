<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Reyesoft\Afip\Tests;

use Reyesoft\Afip\Models\FacturaSinItems;

class FacturaSinItemsTest extends \PHPUnit\Framework\TestCase
{
    public $factura;

    /**
     * FacturaSinItemsTest constructor.
     */
    public function __construct()
    {
        $this->factura = new FacturaSinItems();
        $this->factura->setearConfiguracion($this->getConf());
    }

    public function test_crear_factura_sin_items_sin_arrays(): void
    {
        $this->factura->datos = $this->getDatosFactura(1, 200.00, 0, 0, 200.00, 0, null);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->CAE);
    }

    public function test_crear_factura_sin_items_monot_sin_arrays(): void
    {
        $this->factura->datos = $this->getDatosFactura(11, 200.00, 200, 0, 0.00, 0, null);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->CAE);
    }

    public function test_crear_comprobante_sin_items_con_array_comp_asoc(): void
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
        $this->factura->datos = $this->getDatosFactura(2, 200.00, 0, 0, 200.00, 0, $arrayComprobantesAsociados);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->CAE);
    }

    public function test_crear_factura_sin_items_monot_con_array_tributos(): void
    {
        $arrayOtrosTributos = [
            'otroTributo' => [
                [
                    'codigoTributo' => 2,
                    'descripcion' => 'descripcion',
                    'baseImponible' => 123131,
                    'alicuota' => 123.45,
                    'importe' => 5,
                ],
                [
                    'codigoTributo' => 1,
                    'descripcion' => 'descripcion',
                    'baseImponible' => 123.45,
                    'alicuota' => 123.45,
                    'importe' => 5,
                ],
            ],
        ];
        $this->factura->datos = $this->getDatosFactura(11, 20.00, 10, 10, 0, 0, null, $arrayOtrosTributos);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->CAE);
    }

    public function test_crear_factura_sin_items_con_array_iva(): void
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

        $this->factura->datos = $this->getDatosFactura(1, 176.25, 150, 0, 0, 26.25, null, null, $arraySubtotalesIVA);

        $result = $this->factura->crearComprobante();
        $this->assertNotEmpty($result->CAE);
    }

    public function test_crear_factura_sin_items_con_array_opcionales(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\WsException::class);
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

        $this->factura->datos = $this->getDatosFactura(1, 200.00, 0, 0, 200.00, 0, null, null, null, $arrayOpcionales);
        $this->factura->crearComprobante();
    }

    public function test_crear_factura_sin_items_con_errores_servidor(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\WsException::class);

        $this->factura->datos = $this->getDatosFactura(1, 220.00, 0, 0, 200.00, 0);

        $this->factura->crearComprobante();
    }

    public function test_crear_factura_sin_items_con_errores_validacion(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\ValidationException::class);

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
        $this->factura->datos = $this->getDatosFactura(1, 200.00, 0, 0, 200.00, 'error', $arrayComprobantesAsociados);
        $this->factura->crearComprobante();
    }

    public function getDatosFactura(
        $codigoComprobante,
        $importeTotal,
        $importeGravado,
        $importeSubtotal,
        $importeNoGravado,
        $importeIVA,
        $arrayComprobantesAsociados = null,
        $arrayOtrosTributos = null,
        $arraySubtotalesIVA = null,
        $arrayOpcionales = null)
    {
        $comprobante = [
            'cantidadRegistros' => 1,
            'puntoVenta' => 3,
            'codigoComprobante' => $codigoComprobante,
            'numeroComprobante' => null,
            'codigoConcepto' => 1,
            'codigoDocumento' => 80,
            'numeroDocumento' => 20327936221,
            'fechaEmision' => '20161010',
            'codigoMoneda' => 'PES',
            'cotizacionMoneda' => 1,
            'importeGravado' => $importeGravado,
            'importeNoGravado' => $importeNoGravado,
            'importeExento' => 0,
            'importeSubtotal' => $importeSubtotal,
            'importeIVA' => $importeIVA,
            'importeTotal' => $importeTotal,
            'fechaServicioDesde' => '20160316',
            'fechaServicioHasta' => '20160316',
            'fechaVencimientoPago' => '20160316',
            'arrayComprobantesAsociados' => $arrayComprobantesAsociados,
            'arrayOtrosTributos' => $arrayOtrosTributos,
            'arraySubtotalesIVA' => $arraySubtotalesIVA,
            'arrayOpcionales' => $arrayOpcionales,
        ];

        $comprobante = json_decode(json_encode($comprobante));

        return $comprobante;
    }

    public function test_consultar_factura(): void
    {
        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 20,
            'puntoVenta' => 3,
        ];
        $result = $this->factura->consultarComprobante();

        $this->assertNotEmpty($result);
    }

    public function test_consultar_factura_con_error_servidor(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\WsException::class);

        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 9999,
            'puntoVenta' => 3,
        ];
        $this->factura->consultarComprobante();
    }

    public function test_consultar_factura_con_error_validacion(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\ValidationException::class);

        $this->factura->datos = (object) [
            'codigoComprobante' => 1,
            'numeroComprobante' => 'test',
            'puntoVenta' => 1,
        ];
        $this->factura->consultarComprobante();
    }

    public function test_consultar_caea_por_periodo(): void
    {
        $this->factura->datos = (object) [
            'periodo' => '201603',
            'orden' => 2,
        ];

        $result = $this->factura->consultarCAEAPorPeriodo();
        $this->assertNotEmpty($result);
    }

    //    public function test_solicitar_caea(){
    //        $this->factura->datos = (object)[
    //            'periodo' => '201604',
    //            'orden' =>1,
    //        ];
    //
    //        $result = $this->factura->solicitarCAEA();
    //        $this->assertNotEmpty($result);
    //    }

    public function test_solicitar_caea_error_vencido(): void
    {
        $this->expectException(\Reyesoft\Afip\Exceptions\WsException::class);
        $this->expectExceptionMessage(
            '{"Err":{"Code":15007,"Msg":"El <Periodo> 201603  se encuentra vencido para solicitar CAEA."}}'
        );

        $this->factura->datos = (object) [
            'periodo' => '201603',
            'orden' => 2,
        ];

        $result = $this->factura->solicitarCAEA();
        $this->assertNotEmpty($result);
    }

    public function getConf()
    {
        $dirAfip = '/home/aye/Documents/AFIP';

        return [
            'dir' => [
                'xml_generados' => $dirAfip . '/xml_generados/',
                //                'claves' =>  $dirAfip.'/claves/',
            ],

            'archivos' => [
                'certificado' => $dirAfip . '/claves/ayelenCert.crt',
                'clavePrivada' => $dirAfip . '/claves/privateKey',
            ],

            'cuit' => 20327936221,
        ];
    }
}
