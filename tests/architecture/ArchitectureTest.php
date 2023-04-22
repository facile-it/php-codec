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

    public function testEndpointClasses(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::namespace('Facile\PhpCodec\*'))
            ->shouldNotDependOn()
            ->classes(
                Selector::classname(Codecs::class),
                Selector::classname(Decoders::class),
                Selector::classname(Reporters::class)
            );
    }
}
