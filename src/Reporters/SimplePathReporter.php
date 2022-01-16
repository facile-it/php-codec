<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Reporters;

use Facile\PhpCodec\Reporter;
use function Facile\PhpCodec\strigify;
use Facile\PhpCodec\Validation\Validation;
use Facile\PhpCodec\Validation\VError;

/**
 * @implements Reporter<list<string>>
 */
final class SimplePathReporter implements Reporter
{
    public static function create(): self
    {
        return new self();
    }

    public function report(Validation $validation): array
    {
        return Validation::fold(
            static function (array $errors): array {
                return array_map(
                    [self::class, 'getMessage'],
                    $errors
                );
            },
            static function (): array {
                return ['No errors'];
            },
            $validation
        );
    }

    private static function getMessage(VError $error): string
    {
        if (is_string($error->getMessage())) {
            return $error->getMessage();
        }

        $keys = [];
        $lastName = 'unkown decoder';

        foreach ($error->getContext() as $item) {
            $keys[] = $item->getKey();
            $lastName = $item->getDecoder()->getName();
        }

        $path = implode('/', $keys);

        return sprintf(
            '%sInvalid value %s supplied to decoder "%s"',
            empty($path) ? '' : "$path: ",
            strigify($error->getValue()),
            $lastName
        );
    }
}
