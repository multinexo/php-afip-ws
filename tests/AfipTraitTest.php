<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests;

use Multinexo\Models\AfipConfig;

trait AfipTraitTest
{
    protected function getConfig(string $cuit = '30615459190'): AfipConfig
    {
        $base_path = getcwd();

        $config = new AfipConfig();
        $config->setSandbox(true);
        $config->setCuit($cuit);
        $config->setXmlFolder($base_path . '/tests/resources/' . sha1($cuit) . '/xml_generated/');
        $config->setCertificateFilename($base_path . '/tests/resources/certificate-testing.crt');
        $config->setPrivateKeyFilename($base_path . '/tests/resources/privateKey');

        return $config;
    }
}
