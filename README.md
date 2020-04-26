# PHP library for AFIP Web Services (Argentina)

## Installation

```bash
composer require multinexo/php-afip-ws
```

## Usage

```php
    /** Invoice with items */
    $company_cuit = '20301112227';

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
        $this->getConfig($company_cuit)
    );

    var_dump($data);
```
## Documentation
For more information, visit [official documentation](https://www.afip.gob.ar/ws/documentacion/).

## Testing

```bash
composer coverage
```

### Security
If you discover any security related issues, please contact us at [info@reyesoft.com](mailto:info@reyesoft.com).

### License
The MIT License (MIT). Please see [License File](https://github.com/multinexo/php-afip-ws/blob/master/LICENSE) for more information.
