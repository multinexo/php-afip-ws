<?php
/**
 * This file is part of Multinexo PHP Afip WS package.
 *
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Multinexo\Afip\WSFE;

use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\Traits\Autenticacion as TraitAutenticacion;
use Multinexo\Afip\Traits\Validaciones;

/**
 * Class Wsfe.
 */
class Wsfe extends WsFuncionesInternas
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
    public $wsaa;
    /**
     * @var
     */
    protected $autenticacion;
    /**
     * @var
     */
    public $client;
    /**
     * @var
     */
    protected $authRequest;
    /**
     * @var
     */
    protected $validaciones;
    /**
     * @var
     */
    public $datos;

    /**
     * Wsfe constructor.
     */
    public function __construct()
    {
        $this->ws = 'wsfe';
        $this->resultado = new ManejadorResultados();
    }

    /**
     * Permite crear un comprobante sin items.
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

        $ultimoComprobante = $this->FECompUltimoAutorizado(
            $this->client,
            $this->authRequest,
            $this->datos->codigoComprobante,
            $this->datos->puntoVenta
        )->CbteNro;

        $this->datos = $this->parseFacturaArray($this->datos);
        $this->datos->FeDetReq->FECAEDetRequest->CbteDesde = $ultimoComprobante + 1;
        $this->datos->FeDetReq->FECAEDetRequest->CbteHasta = $ultimoComprobante + 1;
        $result = $this->FECAESolicitar($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite consultar  la  información  correspondiente  a  un  CAEA  previamente  otorgado
     * para un periodo/orden.
     *
     * @return mixed
     *
     * @throws WsException
     * @throws \Multinexo\Afip\Exceptions\ValidationException
     */
    public function consultarCAEAPorPeriodo()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->FECAEAConsultar($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite solicitar Código de Autorización Electrónico Anticipado (CAEA).
     *
     * @return mixed
     *
     * @throws WsException
     * @throws \Multinexo\Afip\Exceptions\ValidationException
     */
    public function solicitarCAEA()
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }

        $this->validarDatos($this->datos, $this->getRules('fe'));
        $result = $this->FECAEASolicitar($this->client, $this->authRequest, $this->datos);

        return $result;
    }

    /**
     * Permite consultar mediante tipo, numero de comprobante y punto de venta los datos  de un comprobante ya emitido.
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
        $result = $this->FECompConsultar($this->client, $this->authRequest, $this->datos);

        return $result;
    }
}
