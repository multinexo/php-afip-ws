<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip\Traits;

use Illuminate\Support\Facades\Log;
use Multinexo\Afip\Exceptions\ValidationException;
use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\WSFE\WsFuncionesInternas as WsfeFunc;
use Multinexo\Afip\WSFE\WsParametros as FeSinItemsParam;
use Multinexo\Afip\WSMTXCA\WsFuncionesInternas as WsmtxcaFunc;
use Multinexo\Afip\WSMTXCA\WsParametros as FeConItemsParam;
use Multinexo\Afip\WSPN3\WsFuncionesInternas as Wspn3Func;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Class Validaciones.
 */
trait Validaciones
{
    /**
     * Devuelve un array con reglas dependiendo del tipo de dato a validar.
     *
     * @param $tipoRegla
     *
     * @return object
     */
    public function getRules($tipoRegla)
    {
        $reglas = [];

        switch ($tipoRegla) {
            case 'fe':

                $wsReglas = [];
                $codComprobantes = $this->codComprobantes();
                $puntosVenta = $this->puntosVentaValidos();
                Log::debug('pos: ' . json_encode($puntosVenta));

                $codDocumento = $this->codDocumento();
                $codMonedas = $this->codMonedas();

                $reglasFeGenerales = [
                    'periodo' => v::notEmpty()->date('Ym'),
                    'orden' => v::notEmpty()->intVal()->between(1, 2)->length(1, 1),
                    'codigoComprobante' => v::in($codComprobantes),
                    'puntoVenta' => v::in($puntosVenta), // fe s/item?
                    'cantidadRegistros' => v::notEmpty()->intVal()->between(1, 9999),
                    'codigoConcepto' => v::in(['1', '2', '3']),
                    'codigoDocumento' => v::in($codDocumento),
                    'numeroDocumento' => v::intVal()->length(1, 11),
                    'codigoMoneda' => v::in($codMonedas),
                    'importeGravado' => v::floatVal()->between(0, 9999999999999.99),
                    'importeNoGravado' => v::floatVal()->between(0, 9999999999999.99),
                    'importeExento' => v::floatVal()->between(0, 9999999999999.99),
                    'importeSubtotal' => v::floatVal()->between(0, 9999999999999.99),
                    'importeIVA' => v::floatVal()->between(0, 9999999999999.99),
                    'importeTotal' => v::floatVal()->between(0, 9999999999999.99),
                    'caea' => v::intVal()->length(14, 14),
                ];

                if ($this->ws == 'wsfe') {
                    $wsReglas = [
                        'cotizacionMoneda' => v::notEmpty()->between(0, 9999999999.999999),
                        'numeroComprobante' => v::optional(v::notEmpty()->intVal()->length(1, 8)),
                        'fechaEmision' => v::optional(v::date('Ymd')),
                        'fechaServicioDesde' => v::optional(v::date('Ymd')),
                        'fechaServicioHasta' => v::optional(v::date('Ymd')),
                        'fechaVencimientoPago' => v::optional(v::date('Ymd')),
                        'arrayComprobantesAsociados' => v::optional(v::objectType()),
                        'arrayOtrosTributos' => v::optional(v::objectType()),
                        'arraySubtotalesIVA' => v::optional(v::objectType()),
                        'arrayOpcionales' => v::optional(v::objectType()),
                        'importeOtrosTributos' => v::optional(v::floatVal()->between(0, 9999999999999.99)),
                    ];
                } elseif ($this->ws == 'wsmtxca') {
                    $wsReglas = [
                        'cotizacionMoneda' => v::notEmpty()->between(0, 9999.999999),
                        'numeroComprobante' => v::optional(v::notEmpty()->intVal()->length(1, 8)),
                        'fechaEmision' => v::optional(v::date('Y-m-d')),
                        'fechaServicioDesde' => v::optional(v::date('Y-m-d')),
                        'fechaServicioHasta' => v::optional(v::date('Y-m-d')),
                        'fechaVencimientoPago' => v::optional(v::date('Y-m-d')),
                        'codigoTipoAutorizacion' => v::optional(v::in(['A', 'E'])),
                        'observaciones' => v::optional(v::stringType()->length(0, 2000)),
                        'importeOtrosTributos' => v::optional(v::floatVal()->between(0, 9999999999999.99)),
                        'arrayItems' => v::notEmpty()->objectType(),
                        'arraySubtotalesIVA' => v::optional(v::objectType()),
                        'arrayComprobantesAsociados' => v::optional(v::objectType()),
                        'arrayOtrosTributos' => v::optional(v::objectType()),
                        'fechaDesde' => v::optional(v::date('Y-m-d')),
                        'fechaHasta' => v::optional(v::date('Y-m-d')),
                    ];
                }
                $reglas = array_merge($reglasFeGenerales, $wsReglas);
                break;
            case 'items':

                $reglas = [
                    'unidadesMtx' => v::optional(v::notEmpty()->intVal()->length(1, 6)),
                    'codigoMtx' => v::optional(v::notEmpty()->stringType()->length(1, 14)),
                    'codigo' => v::optional(v::notEmpty()->stringType()->length(1, 50)),
                    'descripcion' => v::notEmpty()->stringType()->length(1, 4000),
                    'cantidad' => v::optional(v::notEmpty()->floatVal()->between(0, 999999999999999999.999999)),
                    'codigoUnidadMedida' => v::notEmpty()->intVal()->length(1, 6),
                    'precioUnitario' => v::optional(v::notEmpty()->between(0, 999999999999999999.999999)),
                    'importeBonificacion' => v::between(0, 999999999999999999.999999),
                    'codigoCondicionIVA' => v::notEmpty()->intVal()->length(1, 6), //get iva code
                    'importeIVA' => v::optional(v::floatVal()->between(0, 9999999999999.99)),
                    'importeItem' => v::floatVal()->between(0, 9999999999999.99),
                ];
                break;
            case 'iva':

                $codIva = $this->codIva();
                $reglas = [
                    'codigoIva' => v::in($codIva),
                    'importe' => v::floatVal()->between(0, 9999999999999.99),
                ];

                if ($this->ws == 'wsfe') {
                    $regla = ['baseImponible' => v::floatVal()->between(0, 9999999999999.99)];
                    $reglas = array_merge($reglas, $regla);
                }

                break;
            case 'comprobantesAsociados':

                $codComprobantes = $this->codComprobantes();
                $reglas = [
                    'codigoComprobante' => v::in($codComprobantes),
                    'puntoVenta' => v::notEmpty()->intVal()->between(1, 9999)->length(1, 4),
                    'numeroComprobante' => v::optional(v::notEmpty()->intVal()->length(1, 8)),
                ];

                break;
            case 'tributos':

                if ($this->ws == 'wsfe') {
                    $codTributos = $this->codTributos();
                    $reglas = [
                        'codigoTributo' => v::in($codTributos),
                        'descripcion' => v::notEmpty()->stringType()->length(1, 80),
                        'baseImponible' => v::notEmpty()->floatVal()->between(0, 99999999999.99),
                        'alicuota' => v::notEmpty()->floatVal()->between(0, 999.99),
                        'importe' => v::notEmpty()->floatVal()->between(0, 99999999999.99),
                    ];
                } elseif ($this->ws == 'wsmtxca') {
                    $codComprobantes = $this->codComprobantes();
                    $reglas = [
                        'codigoComprobante' => v::in($codComprobantes),
                        'descripcion' => v::notEmpty()->stringType()->length(1, 25),
                        'baseImponible' => v::floatVal()->between(0, 9999999999999.99),
                        'importe' => v::floatVal()->between(0, 9999999999999.99),
                    ];
                }

                break;
            case 'opcionales':

                $codOpcionales = $this->codOpcionales();
                $reglas = [
                    'codigoOpcional' => v::in($codOpcionales),
                    'valor' => v::notEmpty()->stringType()->length(1, 250),
                ];

                break;
            default:
        }

        return (object) $reglas;
    }

    /**
     * Devuelve mensajes de error personalizados.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $errorMessagesEs = [
            'notEmpty' => 'Campo {{name}} obligatorio',
            'date' => '{{name}} debe tener una fecha valida. Ejemplo de formato: {{format}}\'',
            'intVal' => '',
            'between' => '',
            'in' => '',
            'floatVal' => '',
            'length' => '',
            'stringType' => '',
            'objectType' => '',
            'cantidadRegistros' => 'dsfsdfsd',
            'periodo.notEmpty' => 'Campo Periodo: es obligatorio',
            'periodo' => 'Campo Periodo: Debe tener el formato AAAAMM, donde AAAA indica el año y MM el mes en números',
            'orden.notEmpty' => 'Campo Orden: es obligatorio',
            'orden' => 'Campo Orden: Debe ser igual a 1 ó 2.',
            'codigoComprobante.notEmpty' => 'Campo Codigo de Comprobante: es obligatorio',
            'codigoComprobante' => 'Campo Codigo de Comprobante: Debe debe estar comprendido entre 1 y 9998.',
            'numeroComprobante.notEmpty' => 'Campo Numero de Comprobante: es obligatorio',
            'numeroComprobante' => 'Campo Numero de Comprobante: Debe debe estar comprendido entre 1 y 99999999.',
            'puntoVenta.notEmpty' => 'Punto de venta: es obligatorio',
            'puntoVenta' => 'Punto de venta: Debe debe estar comprendido entre 1 y 9998.',
        ];
        $errorMessages = [
        //            'notEmpty' => "The field '{{name}}' is required",
        //            'in' => "The field '{{name}}' must be one of the values: {{haystack}}",
        ];

        return $errorMessages;
    }

    /**
     * Valida los datos de un array.
     *
     * @param $array
     * @param $regla
     *
     * @throws ValidationException
     */
    public function validarDatosArray($array, $regla): void
    {
        // TODO: Los items tienen que ir si o si en el caso de fe wsmtxca
        if (isset($array)) {
            foreach (reset($array) as $dato) {
                $this->validarDatos($dato, $this->getRules($regla));
            }
        }
    }

    /**
     * Valida los datos para un comprobante dependiendo si este tiene o no items asociados.
     *
     * @throws ValidationException
     */
    public function validarDatosFactura(): void
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));

        if (property_exists($this->datos, 'arrayOtrosTributos')) {
            $this->validarDatosArray($this->datos->arrayOtrosTributos, 'tributos');
        }

        if (property_exists($this->datos, 'arraySubtotalesIVA')) {
            $this->validarDatosArray($this->datos->arraySubtotalesIVA, 'iva');
        }

        if (property_exists($this->datos, 'arrayComprobantesAsociados')) {
            $this->validarDatosArray($this->datos->arrayComprobantesAsociados, 'comprobantesAsociados');
        }

        if ($this->ws == 'wsfe') {
            if (property_exists($this->datos, 'arrayOpcionales')) {
                $this->validarDatosArray($this->datos->arrayOpcionales, 'opcionales');
            }
        } elseif ($this->ws == 'wsmtxca') {
            $this->validarDatosArray($this->datos->arrayItems, 'items');
        }
    }

    /**
     * Valida que los datos ingresados cumplan con determinadas reglas.
     *
     * @param $datos
     * @param $reglas
     *
     * @throws ValidationException
     */
    public function validarDatos($datos, $reglas): void
    {
        $validaciones = [];

        foreach ($datos as $key => $dato) {
            $validaciones[] = v::attribute($key, $reglas->{$key});
        }

        $validador = v::allOf($validaciones);

        try {
            $validador->assert($datos);
        } catch (NestedValidationException $exception) {
            $errores = $exception->getMessages();
            $erroresTr = $exception->findMessages($this->getErrorMessages());
            $erroresTr = array_diff($erroresTr, ['']);
            $errors = [];
            foreach ($erroresTr as $error) {
                $errors[] = $error;
            }
            throw new ValidationException($errores);
        }
    }

    /**
     * Retorna array con los códigos comprobantes permitidos para una persona determinada.
     *
     * @return array|mixed
     */
    public function codComprobantes()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposCbte($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->CbteTipo);
        } elseif ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarTiposComprobante($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayTiposComprobante->codigoDescripcion);
        }

        return $codigos;
    }

    /**
     * Retorna array con los puntos de venta válidos.
     *
     * @return array|mixed
     */
    public function puntosVentaValidos()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $result = (new FeSinItemsParam())->FEParamGetPtosVenta($this->client, $this->authRequest);
            if (empty((array) $result->ResultGet)) {
                return [];
            }

            if (count($result->ResultGet->PtoVenta) > 1) {
                $puntosVenta = $result->ResultGet->PtoVenta;
            } else {
                $puntosVenta = $result->ResultGet;
            }

            foreach ($puntosVenta as $puntoVenta) {
                if ($puntoVenta->Bloqueado == 'N') {
                    $codigos[] = $puntoVenta->Nro;
                }
            }
        } elseif ($this->ws == 'wsmtxca') {
            $result = (new FeConItemsParam())->consultarPuntosVenta($this->client, $this->authRequest);

            if (empty((array) $result->arrayPuntosVenta)) {
                return [];
            }

            foreach ($result->arrayPuntosVenta as $puntoVenta) {
                if ($puntoVenta->bloqueado == 'No') {
                    $codigos[] = $puntoVenta->numeroPuntoVenta;
                }
            }
        }

        return $codigos;
    }

    /**
     * Retorna array con los puntos de venta CAE válidos.
     *
     * @return array|mixed
     */
    public function puntosVentaCAEValidos()
    {
        $codigos = [];
        if ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarPuntosVentaCAE($this->client, $this->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //  // Todo: arreglar
        //  $codigos = (new FeSinItemsParam())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //  $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }

    /**
     * Retorna array con los puntos de venta CAEA válidos.
     *
     * @return array|mixed
     */
    public function puntosVentaCAEAValidos()
    {
        $codigos = [];
        if ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarPuntosVentaCAEA($this->client, $this->authRequest);
            if (empty((array) $codigos->arrayPuntosVenta)) {
                return [];
            }
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayPuntosVenta->puntoVenta);
        }
        //elseif ($this->ws == 'wsfe') {
        //            // Todo: arreglar
        //            $codigos = (new FeSinItemsParam())->FEParamGetPtosVenta($this->client, $this->authRequest);
        //            $codigos = array_map(function($o){return $o->Id;}, $codigos->DocTipo);
        //}

        return $codigos;
    }

    /**
     * Retorna array con los codigos de documentos.
     *
     * @return array|mixed
     */
    public function codDocumento()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposDoc($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->DocTipo);
        } elseif ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarTiposDocumento($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayTiposDocumento->codigoDescripcion);
        }

        return $codigos;
    }

    /**
     * Retorna array con los codigos de moneda.
     *
     * @return array|mixed
     */
    public function codMonedas()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposMonedas($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->Moneda);
        } elseif ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarMonedas($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayMonedas->codigoDescripcion);
        }

        return $codigos;
    }

    /**
     * Retorna array con los codigos de iva permitidos para una persona determinada.
     *
     * @return array|mixed
     */
    public function codIva()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposIva($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->IvaTipo);
        } elseif ($this->ws == 'wsmtxca') {
            $codigos = (new FeConItemsParam())->consultarAlicuotasIVA($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayAlicuotasIVA->codigoDescripcion);
        }

        return $codigos;
    }

    /**
     * Retorna array con los codigos de opcionales permitidos para una persona determinada.
     *
     * @return array
     */
    public function codOpcionales()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposOpcional($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->OpcionalTipo);
        }

        return $codigos;
    }

    /**
     * Retorna array con los codigos de tributos permitidos para una persona determinada.
     *
     * @return array
     */
    public function codTributos()
    {
        $codigos = [];
        if ($this->ws == 'wsfe') {
            $codigos = (new FeSinItemsParam())->FEParamGetTiposTributos($this->client, $this->authRequest);
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->TributoTipo);
        }

        return $codigos;
    }

    public function getServerStatus($ws, $cliente)
    {
        switch ($ws) {
            case 'wsmtxca':
                $wsStatus = (new WsmtxcaFunc())->Dummy($cliente);
                break;

            case 'wsfe':
                $wsStatus = (new WsfeFunc())->FEDummy($cliente);
                break;

            case 'wspn3':
                $wsStatus = (new Wspn3Func())->wsDummy($cliente);
                break;

            default:
                throw new WsException('Error en la verificación del servicio');
        }

        return $wsStatus;
    }

    public function checkServerStatus($ws, $cliente)
    {
        $serverStatus = $this->getServerStatus($ws, $cliente);

        switch ($ws) {
            case 'wsmtxca':
                if ($serverStatus->appserver == 'OK'
                    && $serverStatus->authserver == 'OK'
                    && $serverStatus->dbserver == 'OK'
                ) {
                    $status = true;
                } else {
                    $status = false;
                }
                break;

            case 'wsfe':
                if ($serverStatus->AppServer == 'OK'
                    && $serverStatus->DbServer == 'OK'
                    && $serverStatus->AuthServer == 'OK'
                ) {
                    $status = true;
                } else {
                    $status = false;
                }
                break;

            case 'wspn3':
                if ($serverStatus->appserver == 'OK'
                    && $serverStatus->authserver == 'OK'
                    && $serverStatus->dbserver == 'OK'
                ) {
                    $status = true;
                } else {
                    $status = false;
                }
                break;

            default:
                throw new WsException('Error en la verificación del servicio');
        }

        return $status;
    }
}
