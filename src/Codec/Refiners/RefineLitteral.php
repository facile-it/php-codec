<?php declare(strict_types=1);

namespace Pybatt\Codec\Refiners;

use Pybatt\Codec\Refine;

/**
 * @implements Refine<scalar>
 */
class RefineLitteral implements Refine
{
    /** @var scalar */
    private $litteral;

    /**
     * @param scalar $litteral
     */
    public function __construct($litteral)
    {
        $this->litteral = $litteral;
    }

    public function is($u): bool
    {
        return $u === $this->litteral;
    }
}
