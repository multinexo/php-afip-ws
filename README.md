Librería PHP para los Web Services de AFIP (Argentina)
======================================================

-----------

## Intrucciones de instalación

Vía Composer

```javascript
composer require multinexo/php-afip-ws
```

## Ejemplo

```php
	// @todo: make an example
```
## Obtención de certificado CRT
**Una aclaración importante**, hay dos modos para usar los Web Services (WS),
uno es el modo testing o desarrollo,
que es lo que explicaré acá, el otro es el modo “producción” que es el que usa el cliente.
Para poder interactuar con el WS de la afip en necesario la obtención de un
certificado CSR.

### Generar clave privada y certificado de testing

Generar con el [OpenSSL](http://www.slproweb.com/products/Win32OpenSSL.html) la clave privada
y el archivo CSR: (En el caso de GNU/Linux este viene instalado de base).

Linea para generar la clave primaria:
````bash
openssl genrsa -out private 2048
````

Línea para generar el CSR (archivo con el cual se le pide a la AFIP el certificado)
````bash
openssl req -new -key private -subj "/C=AR/O=subj_o/CN=subj_cn/serialNumber=CUITsubj_cuit" -out file.csr
````

Reemplace (siempre en mayúsculas):
* subj_o por el nombre del desarrollador.
* subj_cn por su nombre o server hostname (el nombre de tu máquina).
* subj_cuit por el CUIT sin guiones del desarrollador.

Luego de estos pasos debemos iniciar sesion en [AFIP](http://www.afip.gob.ar).
(Sino tenes clave acercate a tu sucursal mas cercana para obtenerla)

En el apartado **Servicios Administrativos** ingresar a:

> Administrador de Relaciones de clave fiscal.

Luego:

>  Adherir Servicio.

Desplegar:
> Afip / Servicios Interactivos / WSASS - Autogestión Certificados Homologación

Dentro tienes que corroborar y confirmar los datos mostrados en pantalla.

Cuando volvamos a ingresar a la pagina de la AFIP tendremos dentro de nuestros
servicios habilitados *WSASS - Autogestión Certificados Homologación*.
Ingresamos.

En el menú seleccionamos:
> Nuevo certificado

Completamos los campos que aparecen en el formulario. En el campo N°3 copiamos el contenido
del archivo CSR y hacemos click en *Crear DN y obtener certificado*.
Si esta todo correcto copiamos el contenido que se genero y lo guardamos en un archivo de
extensión **.crt**

Solamente nos queda autorizar los servicios de factura electrónica.
En el menú seleccionamos:
> Crear autorización a servicio

En el formulario en el punto N°5, buscamos de a un servicio y creamos autorización de acceso.
- [x] wsfe - Factura electrónica
- [x] wsmtxca - Factura electrónica con Detalle

En el menú *Autorizaciones* podemos ver los servicios autorizados.