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
use Multinexo\Models\AfipConfig;
use Multinexo\Models\Invoice;
use Multinexo\Models\Log;
use Multinexo\Models\Validaciones;

/**
 * Class Wsmtxca (Invoice with items).
 */
class Wsmtxca extends Invoice
{
    use WsmtxcaFuncionesInternas;
    use Validaciones;

    public function __construct(AfipConfig $afipConfig)
    {
        $this->ws = 'wsmtxca';
        $this->resultado = new ManejadorResultados();

        parent::__construct($afipConfig);
    }

    /**
     * Permite crear un comprobante con items.
     *
     * @throws WsException
     */
    public function createInvoice()
    {
        $this->validateDataInvoice();

        try {
            $ultimoComprobante = $this->wsConsultarUltimoComprobanteAutorizado(
                $this->datos->codigoComprobante,
                $this->datos->puntoVenta
            );
        } catch (WsException $e) {
            $codigo = json_decode($e->getMessage())->codigo;
            if ($codigo != 1502) {
                throw new WsException($e->getMessage());
            }
            $ultimoComprobante = 0;
        }
        $this->datos = $this->parseFacturaArray($this->datos);
        $this->datos->numeroComprobante = $ultimoComprobante + 1;

        return $this->wsAutorizarComprobante($this->datos);
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado.
     *
     * @throws WsException
     * @throws \Multinexo\Exceptions\ValidationException
     */
    public function getCAEA()
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));

        return $this->wsConsultarCAEA($this->datos);
    }

    /**
     * Permite solicitar Código de Autorización Electrónico Anticipado (CAEA).
     *
     * @throws WsException
     */
    public function requestCAEA()
    {
        return $this->wsSolicitarCAEA($this->datos);
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado entre un rango de fechas.
     *
     * @throws WsException
     * @throws \Multinexo\Exceptions\ValidationException
     */
    public function consultarCAEAEntreFechas()
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->wsConsultarCAEAEntreFechas($this->datos);

        return isset($result->CAEAResponse) ? $result->CAEAResponse : null;
    }

    /**
     * Permite consultar un comprobante con items ya emitido.
     *
     * @throws WsException
     * @throws \Multinexo\Exceptions\ValidationException
     */
    public function getInvoice()
    {
        $this->validarDatos($this->datos, $this->getRules('fe'));

        return $this->wsConsultarComprobante($this->datos);
    }

    public function getAvailablePosNumbers(): array
    {
        $pos_numbers = [];
        $result = (new WsParametros())->consultarPuntosVenta($this->service->client, $this->service->authRequest);

        if (empty((array) $result->arrayPuntosVenta)) {
            Log::debug('----> getAvailablePosNumbers EMPTY ' . print_r($result, true));

            return [];
        }

        foreach ($result->arrayPuntosVenta as $puntoVenta) {
            Log::debug('----> getAvailablePosNumbers RESULT ' . print_r($puntoVenta, true));
            if ($puntoVenta->bloqueado === 'No') {
                $pos_numbers[] = $puntoVenta->numeroPuntoVenta;
            }
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
        $codigos = (new WsParametros())->consultarTiposComprobante($this->service->client, $this->service->authRequest);

        return array_map(function ($o) {
            return $o->codigo;
        }, $codigos->arrayTiposComprobante->codigoDescripcion ?? []);
    }
}
