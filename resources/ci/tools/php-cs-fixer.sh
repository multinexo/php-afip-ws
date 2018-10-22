#!/bin/sh

## Copyright (C) 1997-2017 Reyesoft <info@reyesoft.com>.
## This file is part of Multinexo. Multinexo can not be copied and/or
## distributed without the express permission of Reyesoft

./vendor/bin/php-cs-fixer fix \
    --config=resources/ci/.php_cs.dist \
    -v --dry-run --stop-on-violation --using-cache=no --path-mode=intersection\
    $SEARCH_PATHS &&
exit $?
