#!/bin/sh

./vendor/bin/php-cs-fixer fix \
    --config=./resources/rules/php-cs-fixer.php \
    --dry-run --stop-on-violation &&

exit $?
