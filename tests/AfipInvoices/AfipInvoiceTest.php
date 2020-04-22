<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\AfipInvoices;

use Multinexo\AfipInvoices\AfipDetail;
use Multinexo\AfipInvoices\AfipInvoice;
use Multinexo\AfipValues\IvaConditionCodes;
use Tests\TestAfipCase;

/**
 * @covers \Multinexo\AfipInvoices\AfipInvoice
 *
 * @internal
 */
final class AfipInvoiceTest extends TestAfipCase
{
    public function testCreateAnInvoiceWithItemsAndGetCae(): void
    {
        $invoice = new AfipInvoice();
        $invoice->addAfipDetail(
            (new AfipDetail())
                ->setQty(2)
                ->setItemCode('P0001')
                ->setDescription('Cool cooler')
                ->setItemNet(50)
                ->setIvaConditionCode(IvaConditionCodes::IVA_21)
                ->setItemNet(55.25)
        );

        $data = $invoice->getDataFromAfip(
            $this->getConfig('20305423174')
        );

        // @todo
        $this->assertTrue(true);
    }
}
