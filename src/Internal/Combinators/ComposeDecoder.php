<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @psalm-template IA
 * @psalm-template A
 * @psalm-template B
 *
 * @template-implements Decoder<IA, B>
 * @psalm-internal Facile\PhpCodec
 */
final class ComposeDecoder implements Decoder
{
    /** @var Decoder<A, B> */
    private \Facile\PhpCodec\Decoder $db;
    /** @var Decoder<IA, A> */
    private \Facile\PhpCodec\Decoder $da;

    /**
     * @psalm-param Decoder<A, B> $db
     * @psalm-param Decoder<IA, A> $da
     */
    public function __construct(
        Decoder $db,
        Decoder $da
    ) {
        $this->db = $db;
        $this->da = $da;
    }

    /**
     * @psalm-param IA      $i
     * @psalm-param Context $context
     * @psalm-return Validation<B>
     *
     * @param mixed $i
     */
    public function validate($i, Context $context): Validation
    {
        return Validation::bind(
            /**
             * @psalm-param A $aValue
             *
             * @param mixed $aValue
             */
            fn ($aValue): Validation => $this->db->validate($aValue, $context),
            $this->da->validate($i, $context)
        );
    }

    public function decode($i): Validation
    {
        return FunctionUtils::standardDecode($this, $i);
    }

    public function getName(): string
    {
        return $this->db->getName();
    }
}
