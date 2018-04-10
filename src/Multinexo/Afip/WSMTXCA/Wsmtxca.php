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

use Illuminate\Support\Facades\Log;
use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\Traits\Autenticacion as TraitAutenticacion;
use Multinexo\Afip\Traits\Validaciones;

/**
 * Class Wsmtxca.
 */
class Wsmtxca extends WsFuncionesInternas
{
    use Validaciones;
    use TraitAutenticacion;

    /**
     * @var string
     */
    protected $ws;
    /**
     * @var
     */
    protected $autenticacion;
    /**
     * @var
     */
    protected $wsaa;
    /**
     * @var
     */
    public $client;
    /**
     * @var
     */
    protected $authRequest;
    /**
     * @var ManejadorResultados
     */
    public $resultado;
    /**
     * @var
     */
    public $datos;
    /**
     * @var
     */
    public $config;

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
     * @return mixed
     *
     * @throws WsException
     */
    public function crearComprobante()
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

        Log::debug((array) $this->datos);
        $result = $this->wsAutorizarComprobante($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado.
     *
     * @return mixed
     *
     * @throws WsException
     * @throws \Multinexo\Afip\Exceptions\ValidationException
     */
    public function consultarCAEA()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->wsConsultarCAEA($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite solicitar Código de Autorización Electrónico Anticipado (CAEA).
     *
     * @return mixed
     *
     * @throws WsException
     */
    public function solicitarCAEA()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        //todo: verificar validación
        //        $this->validarDatos($this->datos,  $this->getRules('fe'));
        $result = $this->wsSolicitarCAEA($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado entre un rango de fechas.
     *
     * @throws WsException
     * @throws \Multinexo\Afip\Exceptions\ValidationException
     */
    public function consultarCAEAEntreFechas()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->wsConsultarCAEAEntreFechas($this->client, $this->authRequest, $this->datos);
        $result = isset($result->CAEAResponse) ? $result->CAEAResponse : null;

        return $result;
    }

    /**
     * Permite consultar un comprobante con items ya emitido.
     *
     * @return mixed
     *
     * @throws WsException
     * @throws \Multinexo\Afip\Exceptions\ValidationException
     */
    public function consultarComprobante()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->wsConsultarComprobante($this->client, $this->authRequest, $this->datos);

        return $result;
    }
}
