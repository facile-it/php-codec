<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Reporters;

use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Reporter;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\VError;

/**
 * @implements Reporter<list<string>>
 */
final class PathReporter implements Reporter
{
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return list<string>
     */
    public function report(Validation $validation): array
    {
        return Validation::fold(
            fn(array $errors): array => \array_map(
                self::getMessage(...),
                $errors
            ),
            fn(): array => ['No errors!'],
            $validation
        );
    }

    public static function getMessage(VError $error): string
    {
        return $error->getMessage()
            ?: \sprintf(
                'Invalid value %s supplied to %s',
                FunctionUtils::strigify($error->getValue()),
                self::getContextPath($error->getContext())
            );
    }

    private static function getContextPath(Context $context): string
    {
        $parts = [];
        foreach ($context as $entry) {
            $parts[] = \sprintf('%s: %s', $entry->getKey(), $entry->getDecoder()->getName());
        }

        return \implode('/', $parts);
    }
}
