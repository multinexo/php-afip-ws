<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

declare(strict_types=1);

namespace Multinexo\Afip\WSMTXCA;

use Multinexo\Afip\Exceptions\WsException;

class WsFuncionesInternas
{
    /**
     * @var
     */
    public $resultado;

    /**
     * Consultar un Comprobante autorizado
     * Permite consultar los datos de un comprobante previamente autorizado, ya sea del tipo Código de Autorización CAE
     * ó CAEA.
     *
     * @param $client
     * @param $authRequest
     * @param $data :
     *              $caea int(14): Especifica el CAEA previamente otorgado sobre el cual se solicita información
     *
     * @return:
     */
    public function wsConsultarComprobante($client, $authRequest, $data)
    {
        $resultado = $client->consultarComprobante(
            [
                'authRequest' => $authRequest,
                'consultaComprobanteRequest' => [
                    'codigoTipoComprobante' => $data->codigoComprobante,
                    'numeroPuntoVenta' => $data->puntoVenta,
                    'numeroComprobante' => $data->numeroComprobante,
                ],
            ]);

        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->comprobante;
    }

    /**
     * Consultar un CAEA previamente otorgado
     * Permite consultar la información correspondiente a CAEA s que hayan tenido vigencia en algún momento dentro de un
     * rango de fechas determinado.
     *
     * @param $client
     * @param $authRequest
     * @param $data :
     *              $caea int(14): Especifica el CAEA previamente otorgado sobre el cual se solicita información
     *
     * @return:
     */
    public function wsConsultarCAEAEntreFechas($client, $authRequest, $data)
    {
        $resultado = $client->consultarCAEAEntreFechas(
            [
                'authRequest' => $authRequest,
                'fechaDesde' => $data->fechaDesde,
                'fechaHasta' => $data->fechaHasta,
            ]);
        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->arrayCAEAResponse;
    }

    /**
     * Consultar un CAEA previamente otorgado
     * Permite consultar la información correspondiente a un CAEA previamente otorgado.
     *
     * @param $client
     * @param $authRequest
     * @param $data :
     *              $caea int(14): Especifica el CAEA previamente otorgado sobre el cual se solicita información
     *
     * @return:
     */
    public function wsConsultarCAEA($client, $authRequest, $data)
    {
        $resultado = $client->consultarCAEA(
            [
                'authRequest' => $authRequest,
                'CAEA' => $data->caea,
            ]);
        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->CAEAResponse;
    }

    /**
     * SolicitarCAEA.
     *
     * @param $client
     * @param $authRequest
     * @param $data
     *
     * @return: Retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *          * cuit: Cuit Emisora del comprobante.
     *          * codigoTipoComprobante: Especifica el tipo de  comprobante.
     *          * numeroPuntoVenta: Indica el  número de  punto de venta del comprobante  autorizado.
     *          * numeroComprobante: Indica el número del  comprobante aprobado.
     *          * fechaEmision: Fecha de emisión del  comprobante.
     *          * CAE: CAE asignado al  comprobante  autorizado.
     *          * fechaVencimientoCAE: Fecha de  vencimiento del CAE  otorgado.
     */
    public function wsSolicitarCAEA($client, $authRequest, $data)
    {
        $resultado = $client->solicitarCAEA(
            [
                'authRequest' => $authRequest,
                'solicitudCAEA' => [
                    'periodo' => $data->periodo,
                    'orden' => $data->orden,
                ],
            ]);
        $this->checkSoapFault($resultado);

        $this->resultado->procesar($resultado);

        return $resultado->CAEAResponse;
    }

    /**
     * Autorizar Comprobante CAE.
     *
     * @param $client
     * @param $authRequest
     * @param $cbte
     *
     * @return: Retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *          * cuit: Cuit Emisora del comprobante.
     *          * codigoTipoComprobante: Especifica el tipo de  comprobante.
     *          * numeroPuntoVenta: Indica el  número de  punto de venta del comprobante  autorizado.
     *          * numeroComprobante: Indica el número del  comprobante aprobado.
     *          * fechaEmision: Fecha de emisión del  comprobante.
     *          * CAE: CAE asignado al  comprobante  autorizado.
     *          * fechaVencimientoCAE: Fecha de  vencimiento del CAE  otorgado.
     */
    public function wsAutorizarComprobante($client, $authRequest, $cbte)
    {
        $resultado = $client->autorizarComprobante(
            [
                'authRequest' => $authRequest,
                'comprobanteCAERequest' => $cbte,
            ]);

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
     * @param $client
     * @param $authRequest
     * @param int(2) $cbteTipo : Tipo de comprobante que se desea consultar
     * @param int(4) $ptoVta   : Punto de venta para el cual se requiera conocer el último número de
     *                         comprobante autorizado
     *
     * @return mixed
     */
    public function wsConsultarUltimoComprobanteAutorizado($client, $authRequest, $cbteTipo, $ptoVta)
    {
        $resultado = $client->consultarUltimoComprobanteAutorizado(
            [
                'authRequest' => $authRequest,
                'consultaUltimoComprobanteAutorizadoRequest' => [
                    'codigoTipoComprobante' => $cbteTipo,
                    'numeroPuntoVenta' => $ptoVta,
                ],
            ]);

        $this->resultado->procesar($resultado);

        return $resultado->numeroComprobante;
    }

    /**
     * Metodo dummy para verificacion de funcionamiento.
     *
     * @param $client
     *
     * @return: Retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *          * AppServer string(2) Servidor de aplicaciones
     *          * DbServer string(2) Servidor de base de datos
     *          * AuthServer string(2) Servidor de autenticación
     *
     * @throws WsException
     */
    public function Dummy($client)
    {
        $result = $client->Dummy();

        if (is_soap_fault($result)) {
            throw new WsException($result->getMessage(), 503);
        }

        return $result;
    }

    /**
     * @param $result
     *
     * @return mixed
     */
    // TODO: Exception
    public function checkSoapFault($result)
    {
        if (is_soap_fault($result)) {
            dump(
                [
                    'SoapErrors' => [
                        'Code' => $result->faultcode,
                        'Description' => $result->faultstring,
                        'Detail' => $result->detail,
                    ],
                ]);
            exit();
        }

        return $result;
    }

    /**
     * Permite adaptar los datos enviados en el array de comprobante a los campos definidos por el ws de la AFIP
     * para la generacion de comprobantes con items.
     *
     * @param $factura
     *
     * @return array|mixed
     */
    public function parseFacturaArray($factura)
    {
        $importeOtrosTributos = 0;
        $importeGravado = 0;
        if (property_exists($factura, 'importeOtrosTributos')) {
            $importeOtrosTributos = $factura->importeOtrosTributos;
        }

        if (property_exists($factura, 'importeGravado')) {
            $importeGravado = $factura->importeGravado;
        }

        $comprobante = [
            'codigoTipoComprobante' => $factura->codigoComprobante,
            'numeroPuntoVenta' => $factura->puntoVenta,
            'numeroComprobante' => $factura->numeroComprobante,
            'fechaEmision' => $factura->fechaEmision,
            'codigoTipoAutorizacion' => $factura->codigoTipoAutorizacion,
            'codigoTipoDocumento' => $factura->codigoDocumento,
            'numeroDocumento' => $factura->numeroDocumento,
            'importeGravado' => $importeGravado,
            'importeNoGravado' => $factura->importeNoGravado,
            'importeExento' => $factura->importeExento,
            'importeSubtotal' => $factura->importeSubtotal,
            'importeOtrosTributos' => $importeOtrosTributos,
            'importeTotal' => $factura->importeTotal,
            'codigoMoneda' => $factura->codigoMoneda,
            'cotizacionMoneda' => $factura->cotizacionMoneda,
            'observaciones' => $factura->observaciones,
            'codigoConcepto' => $factura->codigoConcepto,
            'fechaServicioDesde' => $factura->fechaServicioDesde,
            'fechaServicioHasta' => $factura->fechaServicioHasta,
            'fechaVencimientoPago' => $factura->fechaVencimientoPago,
            'arrayItems' => $factura->arrayItems,
        ];

        if (!property_exists($factura, 'importeOtrosTributos')) {
            unset($comprobante['importeOtrosTributos']);
        }

        if (!property_exists($factura, 'importeGravado')) {
            unset($comprobante['importeGravado']);
        }

        $comprobante = json_decode(json_encode($comprobante));

        if (isset($factura->arraySubtotalesIVA)) {
            $arraySubtotalesIVA = [];
            foreach ($factura->arraySubtotalesIVA->subtotalIVA as $iva) {
                $arraySubtotalesIVA[] = [
                    'codigo' => $iva->codigoIva,
                    'importe' => $iva->importe,
                ];
            }
            $comprobante->{'arraySubtotalesIVA'} = $arraySubtotalesIVA;
        }

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

        if (isset($factura->arrayOtrosTributos)) {
            $arrayOtrosTributos = [];
            foreach ($factura->arrayOtrosTributos->otroTributo as $tributo) {
                $arrayOtrosTributos[] = [
                    'codigo' => $tributo->codigoComprobante,
                    'descripcion' => $tributo->descripcion,
                    'baseImponible' => $tributo->baseImponible,
                    'importe' => $tributo->importe,
                ];
            }

            $comprobante->{'arrayOtrosTributos'} = $arrayOtrosTributos;
        }

        $comprobante = json_decode(json_encode($comprobante));

        return $comprobante;
    }

    /****************************************************************************************************************/

    // TODO: Testar funciones

    /**
     * @param $client
     * @param $authRequest
     * @param $cbte
     *
     * @return mixed
     */
    public function wsInformarComprobanteCAEA($client, $authRequest, $cbte)
    {
        $result = $client->informarComprobanteCAEA(
            [
                'authRequest' => $authRequest,
                'comprobanteCAEARequest' => $cbte,
            ]);
        $this->checkSoapFault($result);
        $errors = $this->resultado->obtenerErrores1($result);
        if (!$errors) {
            $observations = [
                'path' => $result,
                'name' => 'arrayObservaciones',
            ];
            $events = ['path' => $result];
            $this->resultado->procesar($observations, $events);
        } else {
            dump(['Errors' => $errors]);
        }

        return $result->comprobante;
    }

    /**
     * @param $client
     * @param $authRequest
     * @param $caea
     *
     * @return mixed
     */
    public function wsInformarCAEANoUtilizado($client, $authRequest, $caea)
    {
        $result = $client->informarCAEANoUtilizado(
            [
                'authRequest' => $authRequest,
                'CAEA' => $caea,
            ]);
        $this->checkSoapFault($result);
        $errors = $this->resultado->obtenerErrores1($result);
        if (!$errors) {
            $observations = [
                'path' => $result,
                'name' => 'arrayObservaciones',
            ];
            $events = ['path' => $result];
            $this->resultado->procesar($observations, $events);
        } else {
            dump(['Errors' => $errors]);
        }

        return $result->comprobante;
    }

    /**
     * @param $client
     * @param $authRequest
     * @param $caea
     * @param $ptoVta
     *
     * @return mixed
     */
    public function wsInformarCAEANoUtilizadoPtoVta($client, $authRequest, $caea, $ptoVta)
    {
        $result = $client->informarCAEANoUtilizadoPtoVta(
            [
                'authRequest' => $authRequest,
                'CAEA' => $caea,
                'numeroPuntoVenta' => $ptoVta,
            ]);
        $this->checkSoapFault($result);
        $errors = $this->resultado->obtenerErrores1($result);
        if (!$errors) {
            $observations = [
                'path' => $result,
                'name' => 'arrayObservaciones',
            ];
            $events = ['path' => $result];
            $this->resultado->procesar($observations, $events);
        } else {
            dump(['Errors' => $errors]);
        }

        return $result->comprobante;
    }

    /**
     * @param $client
     * @param $authRequest
     * @param $caea
     *
     * @return mixed
     */
    public function wsConsultarPtosVtaCAEANoInformados($client, $authRequest, $caea)
    {
        $result = $client->consultarPtosVtaCAEANoInformados(
            [
                'authRequest' => $authRequest,
                'CAEA' => $caea,
            ]);
        $this->checkSoapFault($result);
        $errors = $this->resultado->obtenerErrores1($result);
        if (!$errors) {
            $observations = [
                'path' => $result,
                'name' => 'arrayObservaciones',
            ];
            $events = ['path' => $result];
            $this->resultado->procesar($observations, $events);
        } else {
            dump(['Errors' => $errors]);
        }

        return $result->arrayPuntosVenta;
    }
}
