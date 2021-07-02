<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\ModelTests;

use Multinexo\Models\CSRFile;
use Tests\TestAfipCase;

/**
 * @internal
 * @covers \Multinexo\Models\CSRFile
 */
final class CsrFileTest extends TestAfipCase
{
    public function testDownloadCSRFile(): void
    {
        $config = $this->getConfig();
        $csr = new CSRFile('My Company', (int) $config->cuit, $config->privatekey_path);

        $csr_path = $csr->saveFileContent();

        $this->assertNotEmpty($csr_path);
        $this->assertIsString($csr_path);
        $this->assertStringContainsString('tmp', $csr_path);
    }
}
