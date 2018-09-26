#!/bin/sh

SEARCH_PATHS='./src/ ./tests/'

./vendor/bin/phpcs --cache --standard=resources/ci/.php-csniffer.xml $SEARCH_PATHS &&

exit $?
