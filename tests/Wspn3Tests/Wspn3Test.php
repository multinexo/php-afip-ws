<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Wspn3Tests;

use Multinexo\WSPN3\Wspn3;
use Tests\TestAfipCase;

/**
 * @internal
 * @covers \Multinexo\WSPN3\Wspn3
 */
final class Wspn3Test extends TestAfipCase
{
    public function testConsultPersonData(): void
    {
        $wspn3 = new Wspn3($this->getConfig());
        // this test sometimes file with this error:
        // gov.afip.padron.core.api.exceptions.PadronSystemException: Index: 0, Size: 0
        $result = $wspn3->consultarDatosPersona('30561785402');
        $this->assertNotEmpty($result);

        $person_data = $wspn3->getResumeWspn3Information($result);
        $this->assertNotEmpty($person_data->legal_name);
        $this->assertNotEmpty($person_data->responsibility);
    }
}
