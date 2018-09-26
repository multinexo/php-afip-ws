<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Afip\WSPN3;

use App\Helpers\GeneralHelper;
use Multinexo\Afip\Exceptions\WsException;
use Multinexo\Afip\Traits\Autenticacion as TraitAutenticacion;
use Multinexo\Afip\Traits\Validaciones;

/**
 * Class Wspn3.
 */
class Wspn3 extends WsFuncionesInternas
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
     * Wspn3 constructor.
     */
    public function __construct()
    {
        $this->ws = 'wspn3';

        $this->resultado = new ManejadorResultados();

    }

    public function consultarDatosPersona($cuit)
    {
        if (!$this->getAutenticacion()) {
            throw new WsException('Error de autenticacion');
        }
        $contribuyente = '<?xml version="1.0" encoding="UTF-8"?>
            <contribuyentePK>
            <id>' . $cuit . '</id>
            </contribuyentePK>';

        return $this->wsGet($this->client, $this->authRequest, $contribuyente);
    }

    public function getResumeWspn3Information($data)
    {
        $helper = new GeneralHelper();

        $parsed_data = [
            'legal_name' => $this->getLegalName($data),
            'description' => $this->getDescription($data),
            'activities_start_date' => '',
            'addresses' => $this->getAddresses($data),
            'phones' => $this->getPhones($data),
            'responsibility' => $this->getResponsibility($data),
        ];

        $clear_data = self::flushNullFromArray($parsed_data);

        return json_decode(json_encode($clear_data));
    }

    /**
     * @param $array
     */
    private static function flushNullFromArray($array)
    {
        foreach ($array as $k => $item) {
            if (!$item) {
                unset($array[$k]);
            }
        }

        return $array;
    }

    private function getLegalName($data)
    {
        $typePerson = $data->persona->tipoPersona;

        $legal_name = null;
        if ($typePerson == 'F') {
            $legal_name = $data->persona->apellido . ', ' . $data->persona->nombre;
        } elseif ($typePerson == 'J') {
            $legal_name = $data->persona->razonSocial;
        }

        return $legal_name;
    }

    private function getDescription($data)
    {
        $description = null;
        if (property_exists($data, 'persona')) {
            if (property_exists($data->persona, 'descripcionCorta')) {
                $description = $data->persona->descripcionCorta;
            }
        }

        return $description;
    }

    private function getAddresses($data)
    {
        $address = null;
        if (property_exists($data, 'domicilios')) {
            $address = $data->domicilios->domicilio;
        }

        return $address;
    }

    private function getPhones($data)
    {
        $phone = null;
        if (property_exists($data, 'telefonos')) {
            $phone = $data->telefonos;
        }

        return $phone;
    }

    private function getResponsibility($data)
    {
        $responsibility = null;

        if (property_exists($data, 'actividades')) {
            if (property_exists($data->actividades, 'actividad')) {
                if (\count($data->actividades->actividad) > 1) {
                    $responsibility = $data->actividades->actividad[0]->actividadPK->estado;
                } else {
                    $responsibility = $data->actividades->actividad->actividadPK->estado;
                }
            }
        }

        return $responsibility;
    }
}
