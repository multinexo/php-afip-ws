#!/bin/sh

./vendor/bin/phpcpd --min-tokens=50 ./src/ ./routes/ \
exit $?
