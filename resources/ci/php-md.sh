#!/bin/sh

SEARCH_PATHS_MD='./src/,./tests/'

./vendor/bin/phpmd $SEARCH_PATHS_MD text resources/rules/phpmd.xml

exit $?
