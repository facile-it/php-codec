<?php

declare(strict_types=1);

namespace ArchitectureAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Encoder;
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
                Selector::classname(Encoder::class),
                Selector::classname(Codec::class)
            );
    }

    public function testCodecsBeAnEndpointClass(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Facile\PhpCodec'))
            ->excluding(Selector::classname(Codecs::class))
            ->shouldNotDependOn()
            ->classes(Selector::classname(Codecs::class));
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
