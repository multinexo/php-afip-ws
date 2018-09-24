#!/bin/sh

sh resources/ci/pipeline/mysql-start.sh &&
sh resources/ci/pipeline/laravel-mysql-env.sh &&
sh resources/ci/pipeline/laravel-migrate.sh &&

exit $?
