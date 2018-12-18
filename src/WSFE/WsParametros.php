<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSFE;

class WsParametros
{
    /** @var ManejadorResultados */
    protected $resultado;

    /**
     * WsParametros constructor.
     */
    public function __construct()
    {
        $this->resultado = new ManejadorResultados();
    }

    /**
     * Recupera la cotizacion de la moneda consultada y su fecha.
     *
     * @param string $monId : Código de moneda de la que se solicita cotización
     *
     * @return \stdClass Retorna la última cotización de la base de datos aduanera de la moneda ingresada.
     *                   * MonCotiz double(4+6) Cotización de la moneda
     *                   * MonId string(3) Código de moneda
     *                   * FchCotiz string(8) Fecha de la cotización. Formato yyyymmdd
     */
    public function FEParamGetCotizacion($client, $authRequest, $monId): \stdClass
    {
        $resultado = $client->FEParamGetCotizacion([
            'Auth' => $authRequest,
            'MonId' => $monId,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetCotizacionResult->ResultGet;
    }

    /**
     * Recupera el listado de puntos de venta registrados y su estado:
     * Permite consultar los puntos de venta para ambos tipos de Código de Autorización (CAE y CAEA) gestionados
     * previamente por la CUIT emisora.
     *
     * @return array PtoVenta: Detalle de los tipos puntos de venta electrónicos:
     *               * Nro int(4) Punto de venta
     *               * EmisionTipo string(8) Identifica si es punto de venta para CAE o CAE
     *               * Bloqueado string(1) Indica si el punto de  venta  esta  bloqueado. De darse esta situación se
     *               deberá ingresar al ABM de puntos de venta a regularizar la situación Valores  S o N
     *               * FchBaja string(8) Indica la fecha de baja en caso de estarlo
     */
    public function FEParamGetPtosVenta($client, $authRequest): array
    {
        $resultado = $client->FEParamGetPtosVenta([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetPtosVentaResult;
    }

    /**
     * Recupera el listado de Tipos de Comprobantes utilizables en servicio de autorización.
     * Permite consultar los tipos de comprobantes habilitados en este WS.
     *
     * @return array CbteTipo: Detalle de los tipos de comprobantes; esta compuesto por los siguientes campos:
     *               * Id int(3)  Código  de comprobante
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposCbte($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposCbte([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposCbteResult->ResultGet;
    }

    /**
     * Recupera el listado de identificadores para el campo Concepto.
     *
     * @return array ConceptoTipo: Detalle de los tipos de conceptos; esta compuesto por los siguientes campos:
     *               * Id int(3)  Código  de concepto
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposConcepto($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposConcepto([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposConceptoResult->ResultGet;
    }

    /**
     * Recupera el listado de Tipos de Documentos utilizables en servicio de autorización.
     *
     * @return array DocTipo: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     *               * Id int(2)  Código de tipo de documento
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposDoc($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposDoc([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposDocResult->ResultGet;
    }

    /**
     * Recupera el listado de Tipos de Iva utilizables en servicio de autorización:
     * Se obtiene la totalidad de alícuotas de IVA posibles de uso en el presente WS, detallando código y descripción.
     *
     * @return array IvaTipo: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     *               * Id int(2) Tipo de IVA
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposIva($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposIva([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposIvaResult->ResultGet;
    }

    /**
     * Recupera el listado de monedas utilizables en servicio de autorización.
     *
     * @return array Moneda: Retorna el universo de tipos de documentos disponibles en el presente WS;
     *               esta compuesto por los siguientes campos:
     *               * Id string(3) Código de moneda
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposMonedas($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposMonedas([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposMonedasResult->ResultGet;
    }

    /**
     * Recupera el listado de identificadores para los campos Opcionales
     * Permite consultar los códigos y descripciones de los tipos de datos Opcionales que se  encuentran habilitados
     * para ser usados en el WS.
     *
     * @return array OpcionalTipo: Detalle de los tipos de datos opcionales; esta compuesto por los siguientes campos:
     *               * Id string(4) Identificador de campo  opcional
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposOpcional($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposOpcional([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposOpcionalResult->ResultGet;
    }

    /**
     * Recupera el listado de los diferentes paises que pueden ser utilizados en el servicio de autorizacion.
     *
     * @return array PaisTipo: Lista de paises; esta compuesto por los siguientes campos:
     *               * Id string(4) Código de país
     *               * Desc string(250) Descripción
     */
    public function FEParamGetTiposPaises($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposPaises([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposPaisesResult->ResultGet;
    }

    /**
     *Recupera el listado de los diferente tributos que pueden ser utilizados en el servicio de autorizacion.
     *
     * @return array TributoTipo: Detalle de los tipos de tributos; esta compuesto por los siguientes campos:
     *               * Id string(2) Código de Tributo
     *               * Desc string(250) Descripción
     *               * FchDesde string(8) Fecha de vigencia desde
     *               * FchHasta string(8) Fecha de vigencia hasta
     */
    public function FEParamGetTiposTributos($client, $authRequest): array
    {
        $resultado = $client->FEParamGetTiposTributos([
            'Auth' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado->FEParamGetTiposTributosResult->ResultGet;
    }
}
