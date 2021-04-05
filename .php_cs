<?php

$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\RiskyRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider([
        'class_attributes_separation' => false,
        'declare_strict_types' => true,
        'indentation_type' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_align' => true
    ]),
]);

$finder = PhpCsFixer\Finder::create();
$autoloadPathProvider = new Facile\CodingStandards\AutoloadPathProvider();
$finder->in($autoloadPathProvider->getPaths());

$config = new PhpCsFixer\Config();
$config->setRules($rulesProvider->getRules());
$config->setRiskyAllowed(true);
$config->setUsingCache(false);
$config->setFinder($finder);

return $config;
