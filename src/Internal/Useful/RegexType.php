<?php declare(strict_types=1);

namespace Facile\Codec\Internal\Useful;

use Facile\Codec\Decoder;
use Facile\Codec\Internal\Arrays\MapRefiner;
use Facile\Codec\Internal\Encode;
use Facile\Codec\Internal\PreconditionFailureExcepion;
use Facile\Codec\Internal\Type;
use Facile\Codec\Validation\Context;
use Facile\Codec\Validation\Validation;

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
            sprintf('regex(%s)', $regex),
            new MapRefiner(),
            Encode::identity()
        );
        $this->regex = $regex;
    }

    public function validate($i, Context $context): Validation
    {
        $matches = [];
        if (preg_match($this->regex, $i, $matches) === false) {
            return Validation::failure($i, $context);
        }

        return Validation::success($matches);
    }

    /**
     * @param mixed $i
     * @return static
     * @psalm-assert string $i
     */
    public function forceCheckPrecondition($i)
    {
        if (!is_string($i)) {
            throw PreconditionFailureExcepion::create('string', $i);
        }

        return $this;
    }
}
