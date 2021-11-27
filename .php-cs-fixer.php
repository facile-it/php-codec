<?php declare(strict_types=1);

$additionalRules = [
    'class_attributes_separation' => false,
    'declare_strict_types' => true,
    'indentation_type' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_align' => true,
    'nullable_type_declaration_for_default_null_value' => true,
    'lambda_not_used_import' => true,
    'phpdoc_add_missing_param_annotation' => true,
];
$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
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
