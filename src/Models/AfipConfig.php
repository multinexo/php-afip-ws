<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

class AfipConfig
{
    /* @var bool */
    public $sandbox;

    /* @var string */
    public $cuit;

    /* @var string */
    public $xml_generated_directory;

    /* @var string */
    public $certificate_path;

    /* @var string */
    public $privateKey_path;

    public function setSandbox(bool $value = null): void
    {
        $this->sandbox = $value ?? false;
    }

    public function setCuit(string $cuit): void
    {
        $this->cuit = $cuit;
    }

    public function setXmlFolder(string $xml_directory): void
    {
        $this->xml_generated_directory = $xml_directory;
    }

    public function setCertificateFilename(string $certificate_path): void
    {
        $this->certificate_path = $certificate_path;
    }

    public function setPrivateKeyFilename(string $privateKey_path): void
    {
        $this->privateKey_path = $privateKey_path;
    }
}
