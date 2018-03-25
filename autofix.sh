#!/bin/sh

SEARCH_PATHS='./src/ ./tests/'

echo "phpcbf..." &&
./vendor/bin/phpcbf $SEARCH_PATHS

echo "phpcs..." &&
./vendor/bin/phpcs $SEARCH_PATHS

echo "php-cs-fixer..." &&
vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --path-mode=intersection $SEARCH_PATHS

git status
