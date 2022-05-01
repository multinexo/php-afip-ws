<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSMTXCA;

use Multinexo\Exceptions\ManejadorResultados;
use Multinexo\Exceptions\WsException;
use Multinexo\Objects\InvoiceObject;
use stdClass;

trait WsmtxcaFuncionesInternas
{
    /** @var ManejadorResultados */
    public $resultado;

    /**
     * Consultar un Comprobante autorizado
     * Permite consultar los datos de un comprobante previamente autorizado, ya sea del tipo Código de Autorización CAE
     * ó CAEA.
     */
    public function wsConsultarComprobante(): stdClass
    {
        $data = $this->datos;
        $resultado = $this->service->client->consultarComprobante(
            [
                'authRequest' => $this->service->authRequest,
                'consultaComprobanteRequest' => [
                    'codigoTipoComprobante' => $data->codigoComprobante,
                    'numeroPuntoVenta' => $data->puntoVenta,
                    'numeroComprobante' => $data->numeroComprobante,
                ],
            ]
        );

        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->comprobante;
    }

    /**
     * Consultar un CAEA previamente otorgado
     * Permite consultar la información correspondiente a CAEA s que hayan tenido vigencia en algún momento dentro de un
     * rango de fechas determinado.
     */
    public function wsConsultarCAEAEntreFechas(): stdClass
    {
        /** @var stdClass $data */
        $data = $this->datos;
        $resultado = $this->service->client->consultarCAEAEntreFechas(
            [
                'authRequest' => $this->service->authRequest,
                'fechaDesde' => $data->fechaDesde,
                'fechaHasta' => $data->fechaHasta,
            ]
        );
        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->arrayCAEAResponse;
    }

    /**
     * Consultar un CAEA previamente otorgado
     * Permite consultar la información correspondiente a un CAEA previamente otorgado.
     */
    public function wsConsultarCAEA(stdClass $data): stdClass
    {
        $resultado = $this->service->client->consultarCAEA(
            [
                'authRequest' => $this->service->authRequest,
                'CAEA' => $data->caea,
            ]
        );
        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->CAEAResponse;
    }

    /**
     * SolicitarCAEA.
     *
     * retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *                * cuit: Cuit Emisora del comprobante.
     *                * codigoTipoComprobante: Especifica el tipo de  comprobante.
     *                * numeroPuntoVenta: Indica el  número de  punto de venta del comprobante  autorizado.
     *                * numeroComprobante: Indica el número del  comprobante aprobado.
     *                * fechaEmision: Fecha de emisión del  comprobante.
     *                * CAE: CAE asignado al  comprobante  autorizado.
     *                * fechaVencimientoCAE: Fecha de  vencimiento del CAE  otorgado
     */
    public function wsSolicitarCAEA(stdClass $data): stdClass
    {
        $resultado = $this->service->client->solicitarCAEA(
            [
                'authRequest' => $this->service->authRequest,
                'solicitudCAEA' => [
                    'periodo' => $data->periodo,
                    'orden' => $data->orden,
                ],
            ]
        );

        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->CAEAResponse;
    }

    /*
     * Autorizar Comprobante CAE.
     *
     * retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *                  * cuit: Cuit Emisora del comprobante.
     *                  * codigoTipoComprobante: Especifica el tipo de  comprobante.
     *                  * numeroPuntoVenta: Indica el  número de  punto de venta del comprobante  autorizado.
     *                  * numeroComprobante: Indica el número del  comprobante aprobado.
     *                  * fechaEmision: Fecha de emisión del  comprobante.
     *                  * CAE: CAE asignado al  comprobante  autorizado.
     *                  * fechaVencimientoCAE: Fecha de  vencimiento del CAE  otorgado
     */

    public function wsAutorizarComprobante(stdClass $payload): stdClass
    {
        $resultado = $this->service->client->autorizarComprobante(
            [
                'authRequest' => $this->service->authRequest,
                'comprobanteCAERequest' => $payload,
            ]
        );

        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // todo:arrayErrores

    /**
     * Consultar el Último Comprobante Autorizado
     * Permite consultar el último número decomprobante autorizado para undeterminado punto de venta y tipo de
     * comprobante, tanto para comprobantes con código de autorización CAE como CAEA.
     *
     * @param int $cbteTipo : Tipo de comprobante que se desea consultar
     * @param int $ptoVta : Punto de venta para el cual se requiera conocer el último número de
     *                    comprobante autorizado
     */
    public function wsConsultarUltimoComprobanteAutorizado($cbteTipo, $ptoVta): ?int
    {
        $resultado = $this->service->client->consultarUltimoComprobanteAutorizado(
            [
                'authRequest' => $this->service->authRequest,
                'consultaUltimoComprobanteAutorizadoRequest' => [
                    'codigoTipoComprobante' => $cbteTipo,
                    'numeroPuntoVenta' => $ptoVta,
                ],
            ]
        );

        $this->resultado->procesar($resultado);

        return $resultado->numeroComprobante;
    }

    /**
     * Metodo dummy para verificacion de funcionamiento.
     *
     * retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     * AppServer string(2) Servidor de aplicaciones
     * DbServer string(2) Servidor de base de datos
     * AuthServer string(2) Servidor de autenticación
     *
     * @throws WsException
     */
    public static function dummy(\SoapClient $client): stdClass
    {
        $result = $client->Dummy();

        if (is_soap_fault($result)) {
            throw new WsException($result->getMessage(), 503);
        }

        return $result;
    }

    // TODO: Exception

    /**
     * @param \SoapFault|stdClass $result
     *
     * @return \SoapFault|stdClass
     */
    public function checkSoapFault($result)
    {
        if (is_soap_fault($result)) {
            var_dump(
                [
                    'SoapErrors' => [
                        'Code' => $result->faultcode,
                        'Description' => $result->faultstring,
                        'Detail' => $result->detail,
                    ],
                ]
            );
        }

        return $result;
    }

    /**
     * Permite adaptar los datos enviados en el array de comprobante a los campos definidos por el ws de la AFIP
     * para la generacion de comprobantes con items.
     */
    public function parseFacturaArray(InvoiceObject $factura): stdClass
    {
        $comprobante = [
            'codigoTipoComprobante' => $factura->codigoComprobante,
            'numeroPuntoVenta' => $factura->puntoVenta,
            'numeroComprobante' => $factura->numeroComprobante,
            'fechaEmision' => $factura->fechaEmision,
            'codigoTipoAutorizacion' => $factura->codigoTipoAutorizacion,
            'codigoTipoDocumento' => $factura->codigoDocumento,
            'numeroDocumento' => $factura->numeroDocumento,
            'importeGravado' => $factura->importeGravado,
            'importeNoGravado' => $factura->importeNoGravado,
            'importeExento' => $factura->importeExento,
            'importeSubtotal' => $factura->importeSubtotal,
            'importeOtrosTributos' => $factura->importeOtrosTributos,
            'importeTotal' => $factura->importeTotal,
            'codigoMoneda' => $factura->codigoMoneda,
            'cotizacionMoneda' => $factura->cotizacionMoneda,
            'observaciones' => $factura->observaciones,
            'codigoConcepto' => $factura->codigoConcepto,
            'fechaServicioDesde' => $factura->fechaServicioDesde,
            'fechaServicioHasta' => $factura->fechaServicioHasta,
            'fechaVencimientoPago' => $factura->fechaVencimientoPago,
            'arrayItems' => $factura->items,
        ];

        if ($factura->importeOtrosTributos == 0) {
            unset($comprobante['importeOtrosTributos']);
        }
        if ($factura->importeGravado == 0) {
            unset($comprobante['importeGravado']);
        }

        // La Fecha de Servicio Desde no debe informarse dado que se indicó Concepto Productos
        if (count($this->datos->items) > 0) {
            unset($comprobante['fechaServicioDesde'], $comprobante['fechaServicioHasta'], $comprobante['fechaVencimientoPago']);
        }

        $comprobante = json_decode(json_encode($comprobante));

        $this->setDocument($factura, $comprobante);

        return json_decode(json_encode($comprobante));
    }

    /**
     * @param InvoiceObject $factura
     */
    private function setDocument($factura, stdClass &$comprobante): void
    {
        $arraySubtotalesIVA = [];
        foreach ($factura->subtotalesIVA as $iva) {
            $arraySubtotalesIVA[] = [
                'codigo' => $iva->codigoIVA,
                'importe' => $iva->importe,
            ];
        }
        $comprobante->{'arraySubtotalesIVA'} = $arraySubtotalesIVA;

        if (isset($factura->arrayComprobantesAsociados)) {
            $arrayComprobantesAsociados = [];
            foreach ($factura->arrayComprobantesAsociados->comprobanteAsociado as $comprobantesAsociado) {
                $arrayComprobantesAsociados[] = [
                    'codigoTipoComprobante' => $comprobantesAsociado->codigoComprobante,
                    'numeroPuntoVenta' => $comprobantesAsociado->puntoVenta,
                    'numeroComprobante' => $comprobantesAsociado->numeroComprobante,
                ];
            }
            $comprobante->{'arrayComprobantesAsociados'} = $arrayComprobantesAsociados;
        }

        $arrayOtrosTributos = [];
        foreach ($factura->arrayOtrosTributos as $tributo) {
            $arrayOtrosTributos[] = [
                'codigo' => $tributo->codigoComprobante,
                'descripcion' => $tributo->descripcion,
                'baseImponible' => $tributo->baseImponible,
                'importe' => $tributo->importe,
            ];
        }
        if (count($arrayOtrosTributos) > 0) {
            $comprobante->{'arrayOtrosTributos'} = $arrayOtrosTributos;
        }
    }

    /**
     * @todo Testar funciones.
     */
    public function wsInformarComprobanteCAEA(string $cbte): string
    {
        $result = $this->service->client->informarComprobanteCAEA(
            [
                'authRequest' => $this->service->authRequest,
                'comprobanteCAEARequest' => $cbte,
            ]
        );
        $this->checkSoapFault($result);

        return $result->comprobante;
    }

    public function wsInformarCAEANoUtilizado(string $caea): string
    {
        $result = $this->service->client->informarCAEANoUtilizado(
            [
                'authRequest' => $this->service->authRequest,
                'CAEA' => $caea,
            ]
        );
        $this->checkSoapFault($result);

        return $result->comprobante;
    }

    public function wsInformarCAEANoUtilizadoPtoVta(string $caea, string $ptoVta): string
    {
        $result = $this->service->client->informarCAEANoUtilizadoPtoVta(
            [
                'authRequest' => $this->service->authRequest,
                'CAEA' => $caea,
                'numeroPuntoVenta' => $ptoVta,
            ]
        );
        $this->checkSoapFault($result);

        return $result->comprobante;
    }

    public function wsConsultarPtosVtaCAEANoInformados(string $caea): array
    {
        $result = $this->service->client->consultarPtosVtaCAEANoInformados(
            [
                'authRequest' => $this->service->authRequest,
                'CAEA' => $caea,
            ]
        );
        $this->checkSoapFault($result);

        return $result->arrayPuntosVenta;
    }
}
