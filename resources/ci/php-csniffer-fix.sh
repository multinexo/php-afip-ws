#!/bin/sh

SEARCH_PATHS='./src/ ./tests/'

./vendor/bin/phpcbf --cache --standard=resources/ci/.php-csniffer.xml $SEARCH_PATHS &&

exit $?
