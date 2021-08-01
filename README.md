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
docker run -it --rm --name php73 -e PHP_EXTENSIONS="" -v "$PWD":/usr/src/app thecodingmachine/php:7.3-v4-cli bash
composer coverage
```
