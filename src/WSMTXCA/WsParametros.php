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
use stdClass;

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

    // Consultar Tipos de Comprobantes.
    public function consultarTiposComprobante(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarTiposComprobante([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Tipos de Documento.
    public function consultarTiposDocumento(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarTiposDocumento([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Alicuotas IVA.
    public function consultarAlicuotasIVA(\SoapClient $client, array $authRequest): stdClass
    {
        return $client->consultarAlicuotasIVA([
            'authRequest' => $authRequest,
        ]);
    }

    // Consultar Condiciones IVA.
    public function consultarCondicionesIVA(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarCondicionesIVA([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Monedas.
    public function consultarMonedas(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarMonedas([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Cotizacion de Moneda.
    public function consultarCotizacionMoneda(\SoapClient $client, array $authRequest, string $codMon): stdClass
    {
        $resultado = $client->consultarCotizacionMoneda([
            'authRequest' => $authRequest,
            'codigoMoneda' => $codMon,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Unidades de Medida.
    public function consultarUnidadesMedida(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarUnidadesMedida([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Puntos de Venta.
    public function consultarPuntosVenta(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarPuntosVenta([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Puntosde Venta CAE.
    public function consultarPuntosVentaCAE(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarPuntosVentaCAE([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Puntos de Venta CAEA.
    public function consultarPuntosVentaCAEA(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarPuntosVentaCAEA([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }

    // Consultar Tipos Tributo.
    public function consultarTiposTributo(\SoapClient $client, array $authRequest): stdClass
    {
        $resultado = $client->consultarTiposTributo([
            'authRequest' => $authRequest,
        ]);

        $this->resultado->procesar($resultado);

        return $resultado;
    }
}
