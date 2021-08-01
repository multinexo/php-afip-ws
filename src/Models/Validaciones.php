<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use Multinexo\Exceptions\ValidationException;
use Multinexo\WSMTXCA\Wsmtxca;
use Multinexo\WSMTXCA\WsParametros;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use stdClass;

trait Validaciones
{
    // Devuelve un array con reglas dependiendo del tipo de dato a validar.
    public function getRules(string $tipoRegla): stdClass
    {
        $rules = [];
        switch ($tipoRegla) {
            case 'fe':
                $rules = $this->getRulesForElectronicInvoice();

                break;
            case 'items':
                $rules = [
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
                $rules = [
                    'codigoIva' => v::in($codIva),
                    'importe' => v::floatVal()->between(0, 9999999999999.99),
                ];

                if ($this->ws === 'wsfe') {
                    $regla = ['baseImponible' => v::floatVal()->between(0, 9999999999999.99)];
                    $rules = array_merge($rules, $regla);
                }

                break;
            case 'comprobantesAsociados':
                $codComprobantes = $this->codComprobantes();
                $rules = [
                    'codigoComprobante' => v::in($codComprobantes),
                    'puntoVenta' => v::notEmpty()->intVal()->between(1, 9999)->length(1, 4),
                    'numeroComprobante' => v::optional(v::notEmpty()->intVal()->length(1, 8)),
                ];

                break;
            case 'tributos':
                if ($this->ws === 'wsfe') {
                    $codTributos = $this->codTributos();
                    $rules = [
                        'codigoTributo' => v::in($codTributos),
                        'descripcion' => v::notEmpty()->stringType()->length(1, 80),
                        'baseImponible' => v::notEmpty()->floatVal()->between(0, 99999999999.99),
                        'alicuota' => v::notEmpty()->floatVal()->between(0, 999.99),
                        'importe' => v::notEmpty()->floatVal()->between(0, 99999999999.99),
                    ];
                } elseif ($this->ws === 'wsmtxca') {
                    $codComprobantes = $this->codComprobantes();
                    $rules = [
                        'codigoComprobante' => v::in($codComprobantes),
                        'descripcion' => v::notEmpty()->stringType()->length(1, 25),
                        'baseImponible' => v::floatVal()->between(0, 9999999999999.99),
                        'importe' => v::floatVal()->between(0, 9999999999999.99),
                    ];
                }

                break;
            case 'opcionales':
                $codOpcionales = $this->codOpcionales();
                $rules = [
                    'codigoOpcional' => v::in($codOpcionales),
                    'valor' => v::notEmpty()->stringType()->length(1, 250),
                ];

                break;
        }

        return (object) $rules;
    }

    private function getRulesForElectronicInvoice(): array
    {
        $wsReglas = [];
        $codComprobantes = $this->codComprobantes();
        $puntosVenta = $this->getAvailablePosNumbers();
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

        if ($this->ws === 'wsfe') {
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
        } elseif ($this->ws === 'wsmtxca') {
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

        return array_merge($reglasFeGenerales, $wsReglas);
    }

    // Devuelve mensajes de error personalizados.
    private function getErrorMessages(): array
    {
        return [
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
    }

    // Valida los datos de un array.
    private function validarDatosArray(array $array, string $regla): void
    {
        if (empty($array)) {
            return;
        }

        // TODO: Los items tienen que ir si o si en el caso de fe wsmtxca
        foreach (reset($array) as $dato) {
            $this->validarDatos($dato, $this->getRules($regla));
        }
    }

    // Valida los datos para un comprobante dependiendo si este tiene o no items asociados.
    public function validateDataInvoice(): void
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));

        if (property_exists($this->datos, 'arrayOtrosTributos')) {
            $this->validarDatosArray((array) $this->datos->arrayOtrosTributos, 'tributos');
        }

        if (property_exists($this->datos, 'arraySubtotalesIVA')) {
            $this->validarDatosArray((array) $this->datos->arraySubtotalesIVA, 'iva');
        }

        if (property_exists($this->datos, 'arrayComprobantesAsociados')) {
            $this->validarDatosArray((array) $this->datos->arrayComprobantesAsociados, 'comprobantesAsociados');
        }

        if ($this->ws === 'wsfe') {
            if (property_exists($this->datos, 'arrayOpcionales')) {
                $this->validarDatosArray((array) $this->datos->arrayOpcionales, 'opcionales');
            }
        } elseif ($this->ws === 'wsmtxca') {
            $this->validarDatosArray((array) $this->datos->arrayItems, 'items');
        }
    }

    // Valida que los datos ingresados cumplan con determinadas reglas.
    public function validarDatos(stdClass $datos, stdClass $reglas): void
    {
        $validaciones = [];

        foreach ((array) $datos as $key => $dato) {
            $validaciones[] = v::attribute($key, $reglas->{$key});
        }

        $validador = v::allOf($validaciones);

        try {
            $validador->assert($datos);
        } catch (NestedValidationException $exception) {
            $errors_translated = array_diff($exception->findMessages($this->getErrorMessages()), ['']);
            $errors = empty($errors_translated) ? ['error' => $exception->getMessage()] : $errors_translated;
            foreach ($errors as $error) {
                throw new ValidationException($error);
            }
        }
    }

    // Retorna array con los codigos de documentos.
    private function codDocumento(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
            $codigos = (array) $this->FEParamGetTiposDoc();
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos['DocTipo']);
        } elseif ($this->ws === 'wsmtxca') {
            /** @var array $authRequest */
            $authRequest = $this->service->authRequest;
            $codigos = (new WsParametros())->consultarTiposDocumento($this->service->client, $authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayTiposDocumento->codigoDescripcion);
        }

        return $codigos;
    }

    // Retorna array con los codigos de moneda.
    private function codMonedas(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
            $codigos = $this->FEParamGetTiposMonedas();
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->Moneda);
        } elseif ($this->ws === 'wsmtxca') {
            /** @var array $authRequest */
            $authRequest = $this->service->authRequest;
            $codigos = (new WsParametros())->consultarMonedas($this->service->client, $authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayMonedas->codigoDescripcion);
        }

        return $codigos;
    }

    // Retorna array con los codigos de iva permitidos para una persona determinada.
    private function codIva(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
            $codigos = $this->FEParamGetTiposIva();
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->IvaTipo);
        } elseif ($this->ws === 'wsmtxca') {
            /** @var array $authRequest */
            $authRequest = $this->service->authRequest;
            $codigos = (new WsParametros())->consultarAlicuotasIVA($this->service->client, $authRequest);
            $codigos = array_map(function ($o) {
                return $o->codigo;
            }, $codigos->arrayAlicuotasIVA->codigoDescripcion);
        }

        return $codigos;
    }

    // Retorna array con los codigos de opcionales permitidos para una persona determinada.
    private function codOpcionales(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
            $codigos = $this->FEParamGetTiposOpcional();
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->OpcionalTipo);
        }

        return $codigos;
    }

    // Retorna array con los codigos de tributos permitidos para una persona determinada.
    private function codTributos(): array
    {
        $codigos = [];
        if ($this->ws === 'wsfe') {
            $codigos = $this->FEParamGetTiposTributos();
            $codigos = array_map(function ($o) {
                return $o->Id;
            }, $codigos->TributoTipo);
        }

        return $codigos;
    }
}
