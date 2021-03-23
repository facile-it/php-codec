<?php declare(strict_types=1);

namespace Facile\Codec;

use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;
use Facile\Codec\Validation\VError;

/**
 * @implements Reporter<list<string>>
 */
class PathReporter implements Reporter
{
    public static function create(): self
    {
        return new self();
    }

    public function report(Validation $validation): array
    {
        return Validation::fold(
            function (array $errors): array {
                return array_map(
                    [self::class, 'getMessage'],
                    $errors
                );
            },
            function (): array {
                return ['No errors!'];
            },
            $validation
        );
    }

    public static function getMessage(VError $error): string
    {
        return $error->getMessage()
            ?: sprintf(
                'Invalid value %s supplied to %s',
                strigify($error->getValue()),
                self::getContextPath($error->getContext())
            );
    }

    private static function getContextPath(Context $context): string
    {
        $parts = [];
        foreach ($context as $entry) {
            $parts[] = sprintf('%s: %s', $entry->getKey(), $entry->getDecoder()->getName());
        }

        return implode('/', $parts);
    }
}
