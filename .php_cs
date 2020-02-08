<?php

$excluded_folders = [
    'vendor',
];
$finder = PhpCsFixer\Finder::create()
    ->exclude($excluded_folders)
    ->notName('AcceptanceTester.php')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony'                          => true,
        'binary_operator_spaces'            => ['align_double_arrow' => true],
        'array_syntax'                      => ['syntax' => 'short'],
        'linebreak_after_opening_tag'       => true,
        'not_operator_with_successor_space' => true,
        'ordered_imports'                   => true,
        'phpdoc_order'                      => true,
        'no_unused_imports'                 => true
    ])
    ->setFinder($finder);