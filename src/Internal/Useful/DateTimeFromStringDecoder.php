<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<string, \DateTimeInterface>
 *
 * @psalm-internal Facile\PhpCodec
 */
final class DateTimeFromStringDecoder implements Decoder
{
    /**
     * @psalm-readonly
     */
    private string $format;

    /**
     * @psalm-readonly
     */
    private bool $strict;

    public function __construct(string $format = \DATE_ATOM, bool $strict = true)
    {
        $this->format = $format;
        $this->strict = $strict;
    }

    public function validate($i, Context $context): Validation
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (! \is_string($i)) {
            return Validation::failure($i, $context);
        }

        $r = \DateTime::createFromFormat($this->format, $i);

        if ($r === false) {
            return Validation::failure($i, $context);
        }

        // In strict mode, check if there were any parsing errors or warnings
        if ($this->strict) {
            $errors = \DateTime::getLastErrors();
            if ($errors !== false && ($errors['error_count'] > 0 || $errors['warning_count'] > 0)) {
                return Validation::failure($i, $context);
            }
        }

        /** @var \DateTimeInterface $r */
        return Validation::success($r);
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'DateTimeFromString';
    }
}
