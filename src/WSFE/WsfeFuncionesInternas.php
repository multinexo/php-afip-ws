<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSFE;

use Multinexo\Exceptions\WsException;

trait WsfeFuncionesInternas
{
    /**
     * @var ManejadorResultados
     */
    protected $resultado;

    /**
     * Método  de autorización de comprobantes  electrónicos por  CAE
     * Solicitud de Código de Autorización Electrónico (CAE).
     *
     * @param \stdClass $authRequest :
     *                               $token: Token devuelto por el WSAA
     *                               $sign: Sign devuelto por el WSAA
     *                               $cuit: Cuit contribuyente (representado o Emisora)
     * @param \stdClass $data : Contiene información del comprobante
     *
     * @throws WsException
     */
    public function FECAESolicitar($client, $authRequest, $data)
    {
        $resultado = $client->FECAESolicitar([
            'Auth' => $authRequest,
            'FeCAEReq' => $data,
        ]);

        $this->resultado->procesar($resultado);

        if (reset($resultado)->FeDetResp->FECAEDetResponse->Resultado == 'R') {
            $observaciones = reset($resultado)->FeDetResp->FECAEDetResponse->Observaciones;
            throw new WsException($observaciones);
        }

        // TODO: Muestro eventos en estructura separada? -> $events = ['path' => $resultado];

        return $resultado->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
    }

    /**
     * Retorna el ultimo comprobante autorizado para el tipo de comprobante / cuit / punto de venta ingresado / Tipo de
     * Emisión.
     *
     * @param \stdClass $authRequest :
     *                               $token: Token devuelto por el WSAA
     *                               $sign: Sign devuelto por el WSAA
     *                               $cuit: Cuit contribuyente (representado o Emisora)
     * @param string $cbteTipo
     * @param string $ptoVta
     *
     * @return \stdClass retorna el último número de comprobante registrado para el punto de venta y tipo de comprobante
     *                   enviado.
     *                   * PtoVta int(4): Punto  de venta
     *                   * CbteTipo int(3): Tipo de comprobante
     *                   * CbteNro long(8): Número de comprobante
     */
    public function FECompUltimoAutorizado($client, $authRequest, $cbteTipo, $ptoVta): \stdClass
    {
        $resultado = $client->FECompUltimoAutorizado([
            'Auth' => $authRequest,
            'PtoVta' => $ptoVta,
            'CbteTipo' => $cbteTipo,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FECompUltimoAutorizadoResult;
    }

    /**
     * Este  método  permite  consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado
     * para un periodo/orden.
     */
    public function FECAEAConsultar($client, $authRequest, $data)
    {
        $resultado = $client->FECAEAConsultar([
            'Auth' => $authRequest,
            'Periodo' => $data->periodo,
            'Orden' => $data->orden,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FECAEAConsultarResult->ResultGet;
    }

    /**
     * Solicitud de Código de Autorización Electrónico Anticipado (CAEA)
     * Podrá ser solicitado dentro de los 5 (cinco) días corridos anteriores al comienzo de cada quincena.
     */
    public function FECAEASolicitar($client, $authRequest, $data)
    {
        $resultado = $client->FECAEASolicitar([
            'Auth' => $authRequest,
            'Periodo' => $data->periodo,
            'Orden' => $data->orden,
        ]);

        // TODO: Function ("FECAEASolicitar") is not a valid method for this service
        $this->resultado->procesar($resultado);

        return $resultado->FECAEASolicitarResult;
    }

    /**
     * Consulta Comprobante emitido y su código:
     * Permite consultar mediante tipo, numero de comprobante y punto de venta los datos  de un comprobante ya emitido.
     */
    public function FECompConsultar($client, $authRequest, $data)
    {
        $resultado = $client->FECompConsultar([
            'Auth' => $authRequest,
            'FeCompConsReq' => [
                'CbteTipo' => $data->codigoComprobante,
                'CbteNro' => $data->numeroComprobante,
                'PtoVta' => $data->puntoVenta,
            ],
        ]);

        // TODO: Function ("FECAEASolicitar") is not a valid method for this service
        $this->resultado->procesar($resultado);

        return $resultado->FECompConsultarResult->ResultGet;
    }

    /**
     * Metodo dummy para verificacion de funcionamiento.
     *
     * @return string retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *                * AppServer string(2) Servidor de aplicaciones
     *                * DbServer string(2) Servidor de base de datos
     *                * AuthServer string(2) Servidor de autenticación
     */
    public function FEDummy($client): string
    {
        $result = $client->FEDummy();

        if (is_soap_fault($result)) {
            throw new WsException($result->getMessage(), 500);
        }

        return $result->FEDummyResult;
    }

    /**
     * @param string $wsdlPath
     * @param string $url
     */
    public function connectToSoapClient($wsdlPath, $url): \SoapClient
    {
        return new \SoapClient($wsdlPath,
            [
                'soap_version' => SOAP_1_2,
                'location' => $url,
                //       'proxy_host'   => "proxy",
                //       'proxy_port'   => 80,
                'exceptions' => 0,
                'trace' => 1,
            ]);
    }

    /**
     * Permite adaptar los datos enviados en el array de comprobante a los campos definidos por el ws de la AFIP
     * para la generacion de comprobantes sin items.
     *
     * @param \stdClass $factura
     *
     * @return array|mixed
     */
    public function parseFacturaArray($factura)
    {
        $comprobante = [
            'FeCabReq' => [
                'CantReg' => $factura->cantidadRegistros,
                'PtoVta' => $factura->puntoVenta,
                'CbteTipo' => $factura->codigoComprobante,
            ],
            'FeDetReq' => [
                'FECAEDetRequest' => [
                    'Concepto' => $factura->codigoConcepto,
                    'DocTipo' => $factura->codigoDocumento,
                    'DocNro' => $factura->numeroDocumento,
                    'CbteDesde' => $factura->numeroComprobante, // todo: depende de la cantidad de fact enviadas
                    'CbteHasta' => $factura->numeroComprobante,
                    'CbteFch' => date('Ymd', strtotime($factura->fechaEmision)),
                    'ImpTotal' => $factura->importeTotal,
                    'ImpTotConc' => $factura->importeNoGravado,
                    'ImpNeto' => $factura->importeGravado,
                    'ImpOpEx' => $factura->importeExento,
                    'ImpTrib' => $factura->importeOtrosTributos,
                    'ImpIVA' => $factura->importeIVA,
                    'MonId' => $factura->codigoMoneda,
                    'MonCotiz' => $factura->cotizacionMoneda,
                ],
            ],
        ];

        $comprobante = json_decode(json_encode($comprobante));

        if (isset($factura->arrayComprobantesAsociados)) {
            $arrayComprobantesAsociados = [];
            foreach ($factura->arrayComprobantesAsociados->comprobanteAsociado as $comprobantesAsociado) {
                $arrayComprobantesAsociados[] = [
                    'Tipo' => $comprobantesAsociado->codigoComprobante,
                    'PtoVta' => $comprobantesAsociado->puntoVenta,
                    'Nro' => $comprobantesAsociado->numeroComprobante,
                ];
            }
            $comprobante->FeDetReq->FECAEDetRequest->{'CbtesAsoc'} = $arrayComprobantesAsociados;
        }

        if (isset($factura->arrayOtrosTributos)) {
            $arrayOtrosTributos = [];
            foreach ($factura->arrayOtrosTributos->otroTributo as $tributo) {
                $arrayOtrosTributos[] = [
                    'Id' => $tributo->codigoTributo,
                    'Desc' => $tributo->descripcion,
                    'BaseImp' => $tributo->baseImponible,
                    'Alic' => $tributo->alicuota,
                    'Importe' => $tributo->importe,
                ];
            }
            $comprobante->FeDetReq->FECAEDetRequest->{'Tributos'} = $arrayOtrosTributos;
        }

        if (isset($factura->arraySubtotalesIVA)) {
            $arraySubtotalesIVA = [];
            foreach ($factura->arraySubtotalesIVA->subtotalIVA as $iva) {
                $arraySubtotalesIVA[] = [
                    'Id' => $iva->codigoIva,
                    'BaseImp' => $iva->baseImponible,
                    'Importe' => $iva->importe,
                ];
            }
            $comprobante->FeDetReq->FECAEDetRequest->{'Iva'} = $arraySubtotalesIVA;
        }

        if (isset($factura->arrayOpcionales)) {
            $arrayOpcionales = [];
            foreach ($factura->arrayOpcionales->Opcional as $opcion) {
                $arrayOpcionales[] = [
                    'Id' => $opcion->codigoOpcional,
                    'Valor' => $opcion->valor,
                ];
            }
            $comprobante->FeDetReq->FECAEDetRequest->{'Opcionales'} = $arrayOpcionales;
        }

        return $comprobante;
    }

    /*/

    // TODO: Testar funciones
    // TODO: error 10002

    /**
     * Método para informar comprobantes emitidos con CAEA:
     * Permite informar para cada CAEA otorgado, la totalidad de los comprobantes emitidos y asociados a cada CAEA.
     * Rendición de comprobantes asociados a un CAEA.
     *
     * @param \stdClass $authRequest
     * @param \stdClass $data
     */
    public function FECAEARegInformativo($client, $authRequest, $data)
    {
        $resultado = $client->FECAEARegInformativo([
            'Auth' => $authRequest,
            'FeCAEARegInfReq' => $data,
        ]);

        $this->resultado->procesar($resultado->FECAEARegInformativoResult);

        return $resultado;
    }

    /**
     * Consulta CAEA informado como sin movimientos:
     * Permite consultar mediante un CAEA, cuales fueron los puntos de venta que fueron notificados  como  sin
     * movimiento.
     *
     * @param \stdClass $authRequest
     * @param string $caea : CAEA otorgado, e identificado como “Sin Movimientos” para determinados
     *                     puntos de venta
     * @param int $ptoVta : Punto de venta vinculado al CAEA informado
     */
    public function FECAEASinMovimientoConsultar($client, $authRequest, $caea, $ptoVta)
    {
        $resultado = $client->FECAEASinMovimientoConsultar([
            'Auth' => $authRequest,
            'CAEA' => $caea,
            'PtoVta' => $ptoVta,
        ]);

        $this->resultado->procesar($resultado->FECAEASinMovimientoConsultarResult);

        return $resultado->FECAEASinMovimientoConsultarResult->ResultGet->FECAEASinMov;
    }

    /**
     * Informa CAEA sin movimientos.
     * Permite informar a la administración cuales fueron los CAEA’s otorgados que no sufrieron movimiento alguno
     * para un determinado punto de venta.
     *
     * @param \stdClass $authRequest
     * @param string $caea : CAEA otorgado, e identificado como “Sin Movimientos” para determinados
     *                     puntos de venta
     * @param int $ptoVta : Punto de venta vinculado al CAEA informado
     */
    public function FECAEASinMovimientoInformar($client, $authRequest, $caea, $ptoVta)
    {
        $resultado = $client->FECAEASinMovimientoInformar([
            'Auth' => $authRequest,
            'CAEA' => $caea,
            'PtoVta' => $ptoVta,
        ]);

        $this->resultado->procesar($resultado->FECAEASinMovimientoInformarResult);

        return $resultado->FECAEASinMovimientoInformarResult;
    }

    /**
     * Retorna la cantidad maxima de registros que puede tener una invocacion al metodo FECAESolicitar /
     * FECAEARegInformativo.
     *
     * @param \stdClass $authRequest
     *
     * @return int Cantidad máxima de registros que se pueden incluir en un Request de solicitud de CAE e Informar
     *             CAEA
     */
    public function FECompTotXRequest($client, $authRequest): int
    {
        $resultado = $client->FECompTotXRequest([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado->FECompTotXRequestResult);

        return $resultado->FECompTotXRequestResult->RegXReq;
    }
}
