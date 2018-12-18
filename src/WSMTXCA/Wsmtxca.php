<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSMTXCA;

use Multinexo\Auth\AuthenticateTrait;
use Multinexo\Auth\Authentication;
use Multinexo\Exceptions\WsException;
use Multinexo\Models\Invoice;
use Multinexo\Traits\Validaciones;
use Multinexo\WSAA\Wsaa;

/**
 * Class Wsmtxca (Invoice with items).
 */
class Wsmtxca extends Invoice
{
    use Validaciones, AuthenticateTrait, WsmtxcaFuncionesInternas;

    /**
     * @var string
     */
    protected $ws;

    /**
     * @var Authentication
     */
    protected $autenticacion;

    /**
     * @var Wsaa
     */
    protected $wsaa;

    /**
     * @var \stdClass
     */
    public $client;

    /**
     * @var \stdClass
     */
    protected $authRequest;

    /**
     * @var ManejadorResultados
     */
    public $resultado;

    /**
     * @var \stdClass
     */
    public $datos;

    /**
     * @var \stdClass
     */
    protected $configuracion;

    /**
     * Wsmtxca constructor.
     */
    public function __construct()
    {
        $this->ws = 'wsmtxca';
        $this->resultado = new ManejadorResultados();
    }

    /**
     * Permite crear un comprobante con items.
     *
     * @throws WsException
     */
    public function createInvoice()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatosFactura();

        try {
            $ultimoComprobante = $this->wsConsultarUltimoComprobanteAutorizado(
                $this->client,
                $this->authRequest,
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

        return $this->wsAutorizarComprobante($this->client, $this->authRequest, $this->datos);
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado.
     *
     * @throws WsException
     * @throws \Multinexo\Exceptions\ValidationException
     */
    public function getCAEA()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos((array) $this->datos, $this->getRules('fe'));

        return $this->wsConsultarCAEA($this->client, $this->authRequest, $this->datos);
    }

    /**
     * Permite solicitar Código de Autorización Electrónico Anticipado (CAEA).
     *
     * @throws WsException
     */
    public function requestCAEA()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        //todo: verificar validación
        //        $this->validarDatos($this->datos,  $this->getRules('fe'));
        return $this->wsSolicitarCAEA($this->client, $this->authRequest, $this->datos);
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado entre un rango de fechas.
     *
     * @throws WsException
     * @throws \Multinexo\Exceptions\ValidationException
     */
    public function consultarCAEAEntreFechas()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos((array) $this->datos, $this->getRules('fe'));
        $result = $this->wsConsultarCAEAEntreFechas($this->client, $this->authRequest, $this->datos);

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
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos((array) $this->datos, $this->getRules('fe'));

        return $this->wsConsultarComprobante($this->client, $this->authRequest, $this->datos);
    }
}
