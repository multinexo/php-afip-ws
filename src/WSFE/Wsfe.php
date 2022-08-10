<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSFE;

use Multinexo\AfipValues\IdCodes;
use Multinexo\Auth\Authentication;
use Multinexo\Exceptions\AfipUnavailableServiceException;
use Multinexo\Exceptions\AfipUnhandledException;
use Multinexo\Exceptions\ManejadorResultados;
use Multinexo\Exceptions\WsException;
use Multinexo\Models\AfipConfig;
use Multinexo\Models\InvoiceWebService;
use Multinexo\Models\Log;
use Multinexo\Models\Validaciones;
use Multinexo\Objects\AssociatedDocumentObject;
use Multinexo\Objects\InvoiceObject;
use Multinexo\Objects\InvoiceResultObject;
use SoapClient;
use stdClass;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Wsfe extends InvoiceWebService
{
    use Validaciones;

    public function __construct(AfipConfig $afipConfig)
    {
        $this->ws = 'wsfe';
        $this->resultado = new ManejadorResultados();

        parent::__construct($afipConfig);
    }

    public function createInvoice(): InvoiceResultObject
    {
        $this->datos->clean();
        $this->clean();
        $this->validateDataInvoice();

        $ultimoComprobante = $this->FECompUltimoAutorizado(
            $this->datos->codigoComprobante,
            $this->datos->puntoVenta
        )->CbteNro;

        $this->parseFacturaArray();
        $this->datos->FeDetReq->FECAEDetRequest->CbteDesde = $ultimoComprobante + 1;
        $this->datos->FeDetReq->FECAEDetRequest->CbteHasta = $ultimoComprobante + 1;

        return $this->parseResult(
            $this->FECAESolicitar()
        );
    }

    private function parseResult(stdClass $response): InvoiceResultObject
    {
        $result = new InvoiceResultObject();
        $date = $response->CbteFch;
        $result->number = (int) $response->CbteDesde;
        $result->emission_date = $date[0] . $date[1] . $date[2] . $date[3] . '-' . $date[4] . $date[5] . '-' . $date[6] . $date[7];

        $result->cae = $response->CAE;
        $result->cae_expiration_date = $response->CAEFchVto;
        if (isset($response->Observaciones)) {
            $result->observation = $response->Observaciones->Obs->Msg . ' (' . $response->Observaciones->Obs->Code . ')';
        }

        return $result;
    }

    private function clean(): void
    {
        if ($this->datos->codigoDocumento == IdCodes::NO_ID && $this->datos->importeIVA <= 0) {
            // Entity is a final costumer AND, because importeIVA==0, its a C invoice.
            // Only document C (not B) requires net=total (kiosko kiosko client case).
            $this->datos->importeGravado = $this->datos->importeSubtotal = $this->datos->importeTotal;
        }
    }

    /*
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado
     * para un periodo/orden.
     */
    public function getCAEA(stdClass $data): stdClass
    {
        $this->validarDatos($data, $this->getRules('fe'));

        return $this->FECAEAConsultar($data);
    }

    // Permite solicitar Código de Autorización Electrónico Anticipado (CAEA).
    public function requestCAEA(stdClass $datos): stdClass
    {
        $this->validarDatos($datos, $this->getRules('fe'));

        return $this->FECAEASolicitar($datos);
    }

    // Permite consultar mediante tipo, numero de comprobante y punto de venta los datos  de un comprobante ya emitido.
    public function getInvoice(): stdClass
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));

        return $this->FECompConsultar();
    }

    /*
     * Método  de autorización de comprobantes  electrónicos por  CAE
     * Solicitud de Código de Autorización Electrónico (CAE).
     */
    private function FECAESolicitar(): stdClass
    {
        /** @var stdClass $data */
        $data = $this->datos;
        $resultado = $this->service->client->FECAESolicitar([
            'Auth' => $this->service->authRequest,
            'FeCAEReq' => $data,
        ]);

        $this->resultado->procesar($resultado);

        if (reset($resultado)->FeDetResp->FECAEDetResponse->Resultado === 'R') {
            $observaciones = reset($resultado)->FeDetResp->FECAEDetResponse->Observaciones->Obs->Msg ?? '';

            if (empty($observaciones)) {
                throw new AfipUnhandledException('FECAEDetResponse: ' . print_r(reset($resultado)->FeDetResp->FECAEDetResponse, true));
            }

            throw new WsException($observaciones);
        }

        // TODO: Muestro eventos en estructura separada? -> $events = ['path' => $resultado];

        return $resultado->FECAESolicitarResult->FeDetResp->FECAEDetResponse;
    }

    /*
     * Retorna el ultimo comprobante autorizado para el tipo de comprobante / cuit / punto de venta ingresado / Tipo de
     * Emisión.
     *
     * return retorna el último número de comprobante registrado para el punto de venta y tipo de comprobante
     *                  enviado.
     *                  * PtoVta int(4): Punto  de venta
     *                  * CbteTipo int(3): Tipo de comprobante
     *                  * CbteNro long(8): Número de comprobante
     */
    public function FECompUltimoAutorizado(int $cbteTipo, int $ptoVta): stdClass
    {
        $resultado = $this->service->client->FECompUltimoAutorizado([
            'Auth' => $this->service->authRequest,
            'PtoVta' => $ptoVta,
            'CbteTipo' => $cbteTipo,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FECompUltimoAutorizadoResult;
    }

    /*
     * Este  método  permite  consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado
     * para un periodo/orden.
     */
    public function FECAEAConsultar(stdClass $data): stdClass
    {
        $resultado = $this->service->client->FECAEAConsultar([
            'Auth' => $this->service->authRequest,
            'Periodo' => $data->periodo,
            'Orden' => $data->orden,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FECAEAConsultarResult->ResultGet;
    }

    /*
     * Solicitud de Código de Autorización Electrónico Anticipado (CAEA)
     * Podrá ser solicitado dentro de los 5 (cinco) días corridos anteriores al comienzo de cada quincena.
     */
    public function FECAEASolicitar(stdClass $data): stdClass
    {
        $resultado = $this->service->client->FECAEASolicitar([
            'Auth' => $this->service->authRequest,
            'Periodo' => $data->periodo,
            'Orden' => $data->orden,
        ]);

        // TODO: Function ("FECAEASolicitar") is not a valid method for this service
        $this->resultado->procesar($resultado->FECAEASolicitarResult);

        return $resultado->FECAEASolicitarResult;
    }

    /*
     * Consulta Comprobante emitido y su código:
     * Permite consultar mediante tipo, numero de comprobante y punto de venta los datos  de un comprobante ya emitido.
     */
    public function FECompConsultar(): stdClass
    {
        /** @var stdClass $data */
        $data = $this->datos;
        $resultado = $this->service->client->FECompConsultar([
            'Auth' => $this->service->authRequest,
            'FeCompConsReq' => [
                'CbteTipo' => $data->codigoComprobante,
                'CbteNro' => $data->numeroComprobante,
                'PtoVta' => $data->puntoVenta,
            ],
        ]);

        $this->resultado->procesar($resultado->FECompConsultarResult);

        return $resultado->FECompConsultarResult->ResultGet;
    }

    /*
     * Metodo dummy para verificacion de funcionamiento.
     *
     * retorna la comprobación vía “ping” de los elementos principales de infraestructura del servicio.
     *                * AppServer string(2) Servidor de aplicaciones
     *                * DbServer string(2) Servidor de base de datos
     *                * AuthServer string(2) Servidor de autenticación
     */
    public static function dummy(SoapClient $client): stdClass
    {
        $result = $client->FEDummy();

        if (is_soap_fault($result)) {
            throw new WsException($result->getMessage(), 500);
        }

        return $result->FEDummyResult;
    }

    public function connectToSoapClient(string $wsdlPath, string $url): SoapClient
    {
        return new SoapClient(
            $wsdlPath,
            [
                'soap_version' => SOAP_1_2,
                'location' => $url,
                //       'proxy_host'   => "proxy",
                //       'proxy_port'   => 80,
                'exceptions' => 0,
                'trace' => 1,
            ]
        );
    }

    /*
     * Permite adaptar los datos enviados en el array de comprobante a los campos definidos por el ws de la AFIP
     * para la generacion de comprobantes sin items.
     */
    private function parseFacturaArray(): void
    {
        $factura = $this->datos;
        $document = [
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
                    'CbteFch' => str_replace('-', '', $factura->fechaEmision),
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

        $document = json_decode(json_encode($document));
        $this->getDataDocument($factura, $document);

        $this->datos = $document;
    }

    /**
     * @param InvoiceObject $invoice
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getDataDocument($invoice, stdClass &$document): void
    {
        $arrayComprobantesAsociados = [];
        foreach ($invoice->comprobantesAsociados as $comprobantesAsociado) {
            /** @var AssociatedDocumentObject $comprobantesAsociado */
            $arrayComprobantesAsociados[] = [
                'Tipo' => $comprobantesAsociado->tipo,
                'PtoVta' => $comprobantesAsociado->punto_de_venta,
                'Nro' => $comprobantesAsociado->numero_comprobante,
            ];
        }
        if (count($arrayComprobantesAsociados) > 0) {
            $document->FeDetReq->FECAEDetRequest->{'CbtesAsoc'} = $arrayComprobantesAsociados;
        }

        $arrayOtrosTributos = [];
        foreach ($invoice->arrayOtrosTributos as $tributo) {
            $arrayOtrosTributos[] = [
                'Id' => $tributo->codigoTributo,
                'Desc' => $tributo->descripcion,
                'BaseImp' => $tributo->baseImponible,
                'Alic' => $tributo->alicuota,
                'Importe' => $tributo->importe,
            ];
        }
        if (count($arrayOtrosTributos) > 0) {
            $document->FeDetReq->FECAEDetRequest->{'Tributos'} = $arrayOtrosTributos;
        }

        $arraySubtotalesIVA = [];
        foreach ($invoice->subtotalesIVA as $iva) {
            $arraySubtotalesIVA[] = [
                'Id' => $iva->codigoIVA,
                'BaseImp' => $iva->baseImponible,
                'Importe' => $iva->importe,
            ];
        }
        if (count($arraySubtotalesIVA) > 0) {
            $document->FeDetReq->FECAEDetRequest->{'Iva'} = $arraySubtotalesIVA;
        }

        if (isset($invoice->arrayOpcionales)) {
            $arrayOpcionales = [];
            foreach ($invoice->arrayOpcionales->Opcional as $opcion) {
                $arrayOpcionales[] = [
                    'Id' => $opcion->codigoOpcional,
                    'Valor' => $opcion->valor,
                ];
            }
            $document->FeDetReq->FECAEDetRequest->{'Opcionales'} = $arrayOpcionales;
        }
    }

    /*
     * Método para informar comprobantes emitidos con CAEA:
     * Permite informar para cada CAEA otorgado, la totalidad de los comprobantes emitidos y asociados a cada CAEA.
     * Rendición de comprobantes asociados a un CAEA.
     */
    public function FECAEARegInformativo(stdClass $data): stdClass
    {
        $resultado = $this->service->client->FECAEARegInformativo([
            'Auth' => $this->service->authRequest,
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
     * @param string $caea : CAEA otorgado, e identificado como “Sin Movimientos” para determinados
     *                     puntos de venta
     * @param int $ptoVta : Punto de venta vinculado al CAEA informado
     */
    public function FECAEASinMovimientoConsultar(string $caea, int $ptoVta): stdClass
    {
        $resultado = $this->service->client->FECAEASinMovimientoConsultar([
            'Auth' => $this->service->authRequest,
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
     * @param string $caea : CAEA otorgado, e identificado como “Sin Movimientos” para determinados
     *                     puntos de venta
     * @param int $ptoVta : Punto de venta vinculado al CAEA informado
     */
    public function FECAEASinMovimientoInformar(string $caea, int $ptoVta): stdClass
    {
        /** @var Authentication $client */
        $client = $this->service->client;
        $resultado = $this->service->client->FECAEASinMovimientoInformar([
            'Auth' => $client->authRequest,
            'CAEA' => $caea,
            'PtoVta' => $ptoVta,
        ]);

        $this->resultado->procesar($resultado->FECAEASinMovimientoInformarResult);

        return $resultado->FECAEASinMovimientoInformarResult;
    }

    /*
     * Retorna la cantidad maxima de registros que puede tener una invocacion al metodo FECAESolicitar /
     * FECAEARegInformativo.
     *
     * return Cantidad máxima de registros que se pueden incluir en un Request de solicitud de CAE e Informar
     *             CAEA
     */
    public function FECompTotXRequest(): int
    {
        $resultado = $this->service->client->FECompTotXRequest([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado->FECompTotXRequestResult);

        return $resultado->FECompTotXRequestResult->RegXReq;
    }

    /**
     * Recupera la cotizacion de la moneda consultada y su fecha.
     *
     * @param string $monId : Código de moneda de la que se solicita cotización
     *
     * Retorna la última cotización de la base de datos aduanera de la moneda ingresada.
     * MonCotiz double(4+6) Cotización de la moneda
     * MonId string(3) Código de moneda
     * FchCotiz string(8) Fecha de la cotización. Formato yyyymmdd
     */
    public function FEParamGetCotizacion(string $monId): stdClass
    {
        $resultado = $this->service->client->FEParamGetCotizacion([
            'Auth' => $this->service->authRequest,
            'MonId' => $monId,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetCotizacionResult->ResultGet;
    }

    /*
     * Recupera el listado de puntos de venta registrados y su estado:
     * Permite consultar los puntos de venta para ambos tipos de Código de Autorización (CAE y CAEA) gestionados
     * previamente por la CUIT emisora.
     *
     * PtoVenta: Detalle de los tipos puntos de venta electrónicos:
     * Nro int(4) Punto de venta
     * EmisionTipo string(8) Identifica si es punto de venta para CAE o CAE
     * Bloqueado string(1) Indica si el punto de  venta  esta  bloqueado. De darse esta situación se
     *               deberá ingresar al ABM de puntos de venta a regularizar la situación Valores  S o N
     * FchBaja string(8) Indica la fecha de baja en caso de estarlo
     */
    public function FEParamGetPtosVenta(): stdClass
    {
        $resultado = $this->service->client->FEParamGetPtosVenta([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetPtosVentaResult;
    }

    /*
     * Recupera el listado de Tipos de Comprobantes utilizables en servicio de autorización.
     * Permite consultar los tipos de comprobantes habilitados en este WS.
     *
     * CbteTipo: Detalle de los tipos de comprobantes; esta compuesto por los siguientes campos:
     * Id int(3)  Código  de comprobante
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    /**
     * @throws AfipUnavailableServiceException
     * @throws AfipUnhandledException
     * @throws WsException
     */
    public function FEParamGetTiposCbte(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposCbte([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        if (isset($resultado->FEParamGetTiposCbteResult->ResultGet)) {
            return $resultado->FEParamGetTiposCbteResult->ResultGet;
        }

        $err = $resultado->FEParamGetTiposCbteResult->Errors->Err
            ?? $resultado->FEParamGetTiposCbteResult->Errors
            ?? null;
        switch ($err->Code ?? null) {
            case 600:
                // ValidacionDeToken: No validaron las fechas del token GenTime, ExpTime, NowUTC:...
                throw new AfipUnavailableServiceException(
                    $resultado->FEParamGetTiposCbteResult->Errors->Msg ?? '',
                    $err->Code
                );
            case null:
                throw new AfipUnhandledException('ResultGet not defined: ' . print_r($resultado, true));
            default:
                throw new WsException(
                    $resultado->FEParamGetTiposCbteResult->Errors->Msg ?? print_r($resultado, true),
                    $err->Code
                );
        }
    }

    /*
     * Recupera el listado de identificadores para el campo Concepto.
     *
     * ConceptoTipo: Detalle de los tipos de conceptos; esta compuesto por los siguientes campos:
     * Id int(3)  Código  de concepto
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposConcepto(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposConcepto([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposConceptoResult->ResultGet;
    }

    /*
     * Recupera el listado de Tipos de Documentos utilizables en servicio de autorización.
     *
     * DocTipo: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     * Id int(2)  Código de tipo de documento
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposDoc(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposDoc([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposDocResult->ResultGet;
    }

    /*
     * Recupera el listado de Tipos de Iva utilizables en servicio de autorización:
     * Se obtiene la totalidad de alícuotas de IVA posibles de uso en el presente WS, detallando código y descripción.
     *
     * IvaTipo: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     * Id int(2) Tipo de IVA
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposIva(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposIva([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposIvaResult->ResultGet;
    }

    /*
     * Recupera el listado de monedas utilizables en servicio de autorización.
     *
     * Moneda: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     * Id string(3) Código de moneda
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposMonedas(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposMonedas([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposMonedasResult->ResultGet;
    }

    /*
     * Recupera el listado de identificadores para los campos Opcionales
     * Permite consultar los códigos y descripciones de los tipos de datos Opcionales que se  encuentran habilitados
     * para ser usados en el WS.
     *
     * OpcionalTipo: Detalle de los tipos de datos opcionales; esta compuesto por los siguientes campos:
     * Id string(4) Identificador de campo  opcional
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposOpcional(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposOpcional([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposOpcionalResult->ResultGet;
    }

    /*
     * Recupera el listado de los diferentes paises que pueden ser utilizados en el servicio de autorizacion.
     *
     * PaisTipo: Lista de paises; esta compuesto por los siguientes campos:
     * Id string(4) Código de país
     * Desc string(250) Descripción
     */
    public function FEParamGetTiposPaises(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposPaises([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposPaisesResult->ResultGet;
    }

    /*
     * Recupera el listado de los diferente tributos que pueden ser utilizados en el servicio de autorizacion.
     *
     * TributoTipo: Detalle de los tipos de tributos; esta compuesto por los siguientes campos:
     * Id string(2) Código de Tributo
     * Desc string(250) Descripción
     * FchDesde string(8) Fecha de vigencia desde
     * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposTributos(): stdClass
    {
        $resultado = $this->service->client->FEParamGetTiposTributos([
            'Auth' => $this->service->authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposTributosResult->ResultGet;
    }

    public function getAvailablePosNumbers(): array
    {
        $pos_numbers = [];
        $result = $this->FEParamGetPtosVenta();

        $fetched_pos_array = $result->ResultGet->PtoVenta ?? [];
        if (!is_array($fetched_pos_array)) {
            // testing dont work like production
            Log::debug('Fix testing env: ' . print_r($result->ResultGet, true));
            $fetched_pos_array = $result->ResultGet ?? [];
        }
        foreach ($fetched_pos_array as $fetched_pos) {
            if (intval($fetched_pos->FchBaja) > 0) {
                continue;
            }

            if ($fetched_pos->Bloqueado !== 'N') {
                continue;
            }

            $pos_numbers[] = $fetched_pos->Nro;
        }

        return $pos_numbers;
    }

    /**
     * Retorna array con los códigos comprobantes permitidos para una persona determinada.
     *
     * @internal
     */
    public function codComprobantes(): array
    {
        $codigos = $this->FEParamGetTiposCbte();

        return array_map(function ($o) {
            return $o->Id;
        }, $codigos->CbteTipo ?? []);
    }
}
