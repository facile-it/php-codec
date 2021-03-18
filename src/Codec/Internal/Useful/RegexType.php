<?php declare(strict_types=1);

namespace Pybatt\Codec\Internal\Useful;

use Pybatt\Codec\Decoder;
use Pybatt\Codec\Internal\Arrays\MapRefiner;
use Pybatt\Codec\Internal\Encode;
use Pybatt\Codec\Internal\PreconditionFailureExcepion;
use Pybatt\Codec\Internal\Type;
use Pybatt\Codec\Validation\Context;
use Pybatt\Codec\Validation\Validation;

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
