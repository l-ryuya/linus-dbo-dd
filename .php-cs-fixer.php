<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
//        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->exclude('public')
    ->exclude('storage')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PER-CS2.0' => true,
            '@PER-CS2.0:risky' => true,
            'declare_strict_types' => true,
            'array_syntax' => ['syntax' => 'short'], // 配列は [] 表記
            'no_unused_imports' => true,             // 未使用useを削除
            'ordered_imports' => ['sort_algorithm' => 'alpha'], // use順
            'class_attributes_separation' => [
                'elements' => [
                    'method' => 'one',
                    'property' => 'one',
                    'trait_import' => 'none',
                ]
            ],
            'phpdoc_to_comment' => false,            // Laravelはphpdoc多用するのでOFF
            'php_unit_test_case_static_method_calls' => false, // $this->assertを維持
        ]
    )
    ->setFinder($finder);
