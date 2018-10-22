#!/bin/sh

./vendor/bin/phpmnd  --hint --include-numeric-string --non-zero-exit-on-violation -q ./src/

exit $?
