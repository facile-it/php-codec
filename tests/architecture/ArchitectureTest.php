<?php

declare(strict_types=1);

namespace ArchitectureAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Reporters;
use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

class ArchitectureTest
{
    public function testAnyInternalClassShouldNotDependFromAnythingOutsideExceptDefinitionInterfaces(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Facile\PhpCodec\Internal'))
            ->shouldNotDependOn()
            ->classes(Selector::namespace('Facile\PhpCodec'))
            ->excluding(
                Selector::namespace('Facile\PhpCodec\Internal'),
                Selector::namespace('Facile\PhpCodec\Validation'),
                Selector::classname(Decoder::class),
            );
    }

    public function testDecodersBeAnEndpointClass(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Facile\PhpCodec'))
            ->excluding(Selector::classname(Decoders::class))
            ->shouldNotDependOn()
            ->classes(Selector::classname(Decoders::class));
    }

    public function testReportersBeAnEndpointClass(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Facile\PhpCodec'))
            ->excluding(Selector::classname(Reporters::class))
            ->shouldNotDependOn()
            ->classes(Selector::classname(Reporters::class));
    }
}
