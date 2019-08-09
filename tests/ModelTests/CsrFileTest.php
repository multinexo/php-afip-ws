<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\ModelTests;

use Multinexo\Models\CSRFile;
use Tests\TestAfipCase;

class CsrFileTest extends TestAfipCase
{
    public function testDownloadCSRFile(): void
    {
        $config = $this->getConfig();
        $csr = new CSRFile('My Company', (int) $config->cuit, $config->privatekey_path);

        $csr_path = $csr->saveFileContent();

        $this->assertNotEmpty($csr_path);
        $this->assertInternalType('string', $csr_path);
        $this->assertContains('tmp', $csr_path);
    }
}
