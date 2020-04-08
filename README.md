# PHP library for AFIP Web Services (Argentina)

## Install

```bash
composer require multinexo/php-afip-ws
```

## Usage

```php
    /** Invoice with items */
    $factura = new Wsmtxca($config);
    
    /** Invoice without items */
    $factura = new Wsfe($config);

    $factura->datos = $data;
    
    /** Make electronic invoice */
    $this->factura->createInvoice();
    
    /**
     * $config must be setted with
     * AfipConfig class
     */
    
    /** Data to send */
     $data = [
        'cantidadRegistros' => 1,
        'puntoVenta' => 3,
        'codigoComprobante' => 6, (Factura B - code_afip)
        'numeroComprobante' => 9562,
        'codigoConcepto' => 1,
        'codigoDocumento' => 21,
        'numeroDocumento' => 20275968579,
        'codigoMoneda' => 'PES',
        'cotizacionMoneda' => 1,
        'importeGravado' => 21.00,
        'importeNoGravado' => 10.50,
        'importeExento' => 13.00,
        'importeOtrosTributos' => 17.00,
        'importeSubtotal' => 18.00,
        'importeIVA' => 10.00,
        'importeTotal' => 28.00,
        'fechaServicioDesde' => null,
        'fechaServicioHasta' => null,
        'fechaVencimientoPago' => null,
        'arraySubtotalesIVA' => [],
        'arrayComprobantesAsociados' => null,
     ];
      
    /** Invoice without items */
    $data['fechaEmision'] = date('Ymd');
               
    /** Invoice with items */
    $data['fechaEmision'] = date('Y-m-d');
    $data['codigoTipoAutorizacion'] = null;
    $data['observaciones'] = null;
    $data['arrayItems'] = ['item' => $items];
    
    $items[] = [
        'unidadesMtx' => 1,
        'codigoMtx' => 4532, (code_detail)
        'codigo' => 4532, (code_detail)
        'descripcion' => 'descripcion',
        'cantidad' => 2,
        'codigoUnidadMedida' => 7,
        'precioUnitario' => 43.00,
        'importeBonificacion' => 0.00,
        'codigoCondicionIVA' => 5,
        'importeIVA' => 10.50,
        'importeItem' => 49.00,
    ];
```
## Documentation
For more information, consult the [documentation official](https://www.afip.gob.ar/ws/documentacion/).

## Testing

```bash
composer coverage
```

### Security
If you discover any security related issues, please contact us at [info@reyesoft.com](mailto:info@reyesoft.com).

### License
The MIT License (MIT). Please see [License File](https://github.com/multinexo/php-afip-ws/blob/master/LICENSE) for more information.
