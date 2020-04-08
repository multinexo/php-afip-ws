<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\WSPN3;

use Multinexo\Auth\Authentication;
use Multinexo\Exceptions\ManejadorResultados;
use Multinexo\Exceptions\WsException;
use Multinexo\Models\AfipConfig;
use stdClass;

class Wspn3
{
    private $service;

    private $ws;

    private $resultado;

    public $datos;

    /**
     * Wspn3 constructor.
     */
    public function __construct(AfipConfig $afipConfig)
    {
        $this->ws = 'wspn3';
        $this->service = new Authentication($afipConfig, $this->ws);
        $this->resultado = new ManejadorResultados();
    }

    public function consultarDatosPersona(string $cuit): stdClass
    {
        $contribuyente = '<?xml version="1.0" encoding="UTF-8"?>
            <contribuyentePK>
            <id>' . $cuit . '</id>
            </contribuyentePK>';

        return $this->wsGet($contribuyente);
    }

    public function getResumeWspn3Information(stdClass $data)
    {
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
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    private static function flushNullFromArray(array $array): array
    {
        foreach ($array as $k => $item) {
            if (!$item) {
                unset($array[$k]);
            }
        }

        return $array;
    }

    private function getLegalName(stdClass $data)
    {
        $typePerson = $data->persona->tipoPersona;

        $legal_name = null;
        if ($typePerson === 'F') {
            $legal_name = $data->persona->apellido . ', ' . $data->persona->nombre;
        } elseif ($typePerson === 'J') {
            $legal_name = $data->persona->razonSocial;
        }

        return $legal_name;
    }

    private function getDescription(stdClass $data)
    {
        $description = null;
        if (property_exists($data, 'persona')) {
            if (property_exists($data->persona, 'descripcionCorta')) {
                $description = $data->persona->descripcionCorta;
            }
        }

        return $description;
    }

    private function getAddresses(stdClass $data)
    {
        $address = null;
        if (property_exists($data, 'domicilios')) {
            $address = $data->domicilios->domicilio;
        }

        return $address;
    }

    private function getPhones(stdClass $data)
    {
        $phone = null;
        if (property_exists($data, 'telefonos')) {
            $phone = $data->telefonos;
        }

        return $phone;
    }

    private function getResponsibility(stdClass $data)
    {
        $responsibility = null;

        if (property_exists($data, 'actividades')) {
            if (property_exists($data->actividades, 'actividad')) {
                $responsibility = $data->actividades->actividad->actividadPK->estado;
            }
        }

        return $responsibility;
    }

    public function wsGet($contribuyente)
    {
        $resultado = $this->service->client->get(
            $contribuyente,
            $this->service->authRequest->token,
            $this->service->authRequest->sign
        );

        $this->resultado->procesar($resultado);

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte

        return json_decode(json_encode($resultado));
    }

    public static function dummy($client): stdClass
    {
        $resultado = $client->dummy();

        if (is_soap_fault($resultado)) {
            throw new WsException($resultado->getMessage(), 500);
        }

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte

        return json_decode(json_encode($resultado));
    }
}
