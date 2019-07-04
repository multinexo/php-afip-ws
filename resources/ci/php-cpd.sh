#!/bin/sh

./vendor/bin/phpcpd --min-tokens=50 ./src/ \
--regexps-exclude=ManejadorResultados.php \
&&
exit $?
