<?php declare(strict_types=1);

namespace Tests\Pybatt\Codec\Internal;

use Pybatt\Codec\Internal\PreconditionFailureExcepion;
use PHPUnit\Framework\TestCase;

class PreconditionFailureExcepionTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertSame(
            'Bad codec composition: expecting input to be of type "string", given "integer"',
            PreconditionFailureExcepion::create('string', 1)->getMessage()
        );

        self::assertSame(
            'Bad codec composition: expecting input to be of type "stdClass", given "array"',
            PreconditionFailureExcepion::create(\stdClass::class, [1,2])->getMessage()
        );
    }
}
