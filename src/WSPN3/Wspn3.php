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
    /** @var Authentication */
    private $service;
    /** @var string */
    private $ws;
    /** @var ManejadorResultados */
    private $resultado;
    /** @var mixed */
    public $datos;

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

    public function getResumeWspn3Information(stdClass $data): stdClass
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

    private static function flushNullFromArray(array $array): array
    {
        foreach ($array as $k => $item) {
            if (!$item) {
                unset($array[$k]);
            }
        }

        return $array;
    }

    private function getLegalName(stdClass $data): ?string
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

    private function getDescription(stdClass $data): ?string
    {
        $description = null;
        if (property_exists($data, 'persona')) {
            if (property_exists($data->persona, 'descripcionCorta')) {
                $description = $data->persona->descripcionCorta;
            }
        }

        return $description;
    }

    private function getAddresses(stdClass $data): array
    {
        $address = [];
        if (property_exists($data, 'domicilios')) {
            $address = $data->domicilios->domicilio;
        }

        return $address;
    }

    private function getPhones(stdClass $data): stdClass
    {
        $phone = new stdClass();
        if (property_exists($data, 'telefonos')) {
            $phone = $data->telefonos;
        }

        return $phone;
    }

    private function getResponsibility(stdClass $data): string
    {
        $responsibility = '';
        if (property_exists($data, 'actividades')) {
            if (property_exists($data->actividades, 'actividad')) {
                $responsibility = $data->actividades->actividad->actividadPK->estado;
            }
        }

        return $responsibility;
    }

    public function wsGet(string $contribuyente): stdClass
    {
        /** @var stdClass $authRequest */
        $authRequest = $this->service->authRequest;
        $resultado = $this->service->client->get(
            $contribuyente,
            $authRequest->token,
            $authRequest->sign
        );

        $this->resultado->procesar((object) $resultado);

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte

        return json_decode(json_encode($resultado));
    }

    public static function dummy(\SoapClient $client): stdClass
    {
        $resultado = $client->dummy();

        if (is_soap_fault($resultado)) {
            throw new WsException($resultado->getMessage(), 500);
        }

        $resultado = simplexml_load_string($resultado); // TODO: Colocar el función aparte

        return json_decode(json_encode($resultado));
    }
}
