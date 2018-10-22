<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\WSMTXCA;

class WsParametros
{
    /**
     * WsParametros constructor.
     */
    public function __construct()
    {
        $this->resultado = new ManejadorResultados();
    }

    // TODO: Analizar si se utiliza esta funcion

    /**
     * @param $serviceName
     * @param $client
     * @param $authRequest
     */
    public function consultar($serviceName, $client, $authRequest)
    {
        $service = 'consultar' . $serviceName;
        $array = 'array' . $serviceName;
        $resultado = $client->{$service}([
            'authRequest' => $authRequest,
        ]);

        return $resultado->{$array}->codigoDescripcion;
    }

    /**
     * Consultar Tipos de Comprobantes.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarTiposComprobante($client, $authRequest)
    {
        $resultado = $client->consultarTiposComprobante([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Tipos de Documento.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarTiposDocumento($client, $authRequest)
    {
        $resultado = $client->consultarTiposDocumento([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Alicuotas IVA.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarAlicuotasIVA($client, $authRequest)
    {
        return $client->consultarAlicuotasIVA([
            'authRequest' => $authRequest,
        ]);
    }

    /**
     * Consultar Condiciones IVA.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarCondicionesIVA($client, $authRequest)
    {
        $resultado = $client->consultarCondicionesIVA([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Monedas.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarMonedas($client, $authRequest)
    {
        $resultado = $client->consultarMonedas([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Cotizacion de Moneda.
     *
     * @param $client
     * @param $authRequest
     * @param $codMon
     */
    public function consultarCotizacionMoneda($client, $authRequest, $codMon)
    {
        $resultado = $client->consultarCotizacionMoneda([
            'authRequest' => $authRequest,
            'codigoMoneda' => $codMon,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Unidades de Medida.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarUnidadesMedida($client, $authRequest)
    {
        $resultado = $client->consultarUnidadesMedida([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Puntos de Venta.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarPuntosVenta($client, $authRequest)
    {
        $resultado = $client->consultarPuntosVenta([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Puntosde Venta CAE.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarPuntosVentaCAE($client, $authRequest)
    {
        $resultado = $client->consultarPuntosVentaCAE([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Puntos de Venta CAEA.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarPuntosVentaCAEA($client, $authRequest)
    {
        $resultado = $client->consultarPuntosVentaCAEA([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    /**
     * Consultar Tipos Tributo.
     *
     * @param $client
     * @param $authRequest
     */
    public function consultarTiposTributo($client, $authRequest)
    {
        $resultado = $client->consultarTiposTributo([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }
}
