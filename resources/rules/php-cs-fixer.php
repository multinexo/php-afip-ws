<?php
$project_name = 'php-afip-ws';
$config = require __DIR__.'/../../vendor/reyesoft/ci/php/rules/php-cs-fixer.dist.php';

// rules override
$rules = array_merge(
    $config->getRules(),
    [
        'strict_comparison' => false,
        'no_useless_else' => false,
        'php_unit_method_casing' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
    ]
);

return $config
    ->setRules($rules)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('./src')
            ->in('./tests')
    );
