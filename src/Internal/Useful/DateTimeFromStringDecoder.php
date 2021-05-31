<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Decoder;
use function Facile\PhpCodec\Internal\standardDecode;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @template-implements Decoder<string, \DateTimeInterface>
 * @psalm-internal Facile\PhpCodec
 */
final class DateTimeFromStringDecoder implements Decoder
{
    /**
     * @var string
     * @psalm-readonly
     */
    private $format;

    public function __construct(string $format = \DATE_ATOM)
    {
        $this->format = $format;
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

        return Validation::success($r);
    }

    public function decode($i): Validation
    {
        return standardDecode($this, $i);
    }

    public function getName(): string
    {
        return 'DateTimeFromString';
    }
}
