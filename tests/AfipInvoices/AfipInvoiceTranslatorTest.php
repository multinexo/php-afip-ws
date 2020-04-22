<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Tests\AfipInvoices;

use Multinexo\AfipInvoices\AfipInvoice;
use Multinexo\AfipInvoices\AfipInvoiceTranslator;
use Multinexo\AfipValues\IvaConditionCodes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Multinexo\AfipInvoices\AfipInvoiceTranslator
 *
 * @internal
 */
final class AfipInvoiceTranslatorTest extends TestCase
{
    public function testDataHasRequiredFields(): void
    {
        $invoice = new AfipInvoice();
        $invoice->setReceiptNumber(5);
        $invoice->createAfipDetail()
            ->setQty(2)
            ->setItemCode('P0001')
            ->setDescription('Cool cooler')
            ->setItemNet(50)
            ->setIvaConditionCode(IvaConditionCodes::IVA_21)
            ->setItemNet(55.25);

        $data = (new AfipInvoiceTranslator($invoice))->getDataWsmtxcaArray();

        $this->assertSame(110.5, $data['importeGravado']);
        $this->assertSame(110.5, $data['importeSubtotal']);
        $this->assertSame(99, $data['codigoDocumento']);
    }

    public function testInvoiceDataTotalsHasTheSameOnLetterAAndB(): void
    {
        // @todo
        $this->assertTrue(true);
    }
}
