<?php

declare(strict_types=1);

namespace Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Internal\Arrays\MapRefiner;
use Facile\PhpCodec\Internal\Encode;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Validation\Context;
use Facile\PhpCodec\Validation\Validation;

/**
 * @extends Type<string[], string, string[]>
 */
class RegexType extends Type
{
    /** @var string */
    private $regex;

    public function __construct(string $regex)
    {
        parent::__construct(
            \sprintf('regex(%s)', $regex),
            new MapRefiner(),
            Encode::identity()
        );
        $this->regex = $regex;
    }

    public function validate($i, Context $context): Validation
    {
        $matches = [];
        if (\preg_match($this->regex, $i, $matches) === false) {
            return Validation::failure($i, $context);
        }

        return Validation::success($matches);
    }
}
