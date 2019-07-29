<?php
/**
 * Copyright (C) 1997-2018 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\Afip;

use Multinexo\WSPN3\Wspn3;
use Tests\TestAfipCase;

class Wspn3Test extends TestAfipCase
{
    public function testConsultPersonData(): void
    {
        $wspn3 = new Wspn3($this->getConfig());
        $result = $wspn3->consultarDatosPersona('30561785402');
        $this->assertNotEmpty($result);
    }
}
