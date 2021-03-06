<?php

declare(strict_types=1);

namespace ArchitectureAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Encoder;
use PhpAT\Rule\Rule;
use PhpAT\Selector\Selector;

class ArchitectureTest extends \PhpAT\Test\ArchitectureTest
{
    public function testCodecsShouldntBeUsed(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::havePath('*'))
            ->excludingClassesThat(Selector::haveClassName(Codecs::class))
            ->mustNotDependOn()
            ->classesThat(Selector::haveClassName(Codecs::class))
            ->build();
    }

    public function testAnyInternalClassShouldNotDependFromAnythingOutsideExceptDefinitionInterfaces(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::havePath('Internal/*'))
            ->mustNotDependOn()
            ->classesThat(Selector::havePath('*'))
            ->excludingClassesThat(Selector::havePath('Internal/*'))
            ->andExcludingClassesThat(Selector::havePath('Validation/*'))
            ->andExcludingClassesThat(Selector::haveClassName(Decoder::class))
            ->andExcludingClassesThat(Selector::haveClassName(Encoder::class))
            ->andExcludingClassesThat(Selector::haveClassName(Codec::class))
            ->build();
    }

    public function testDecodersMustExposeEveryInteralDecoder(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName(Decoders::class))
            ->mustDependOn()
            ->classesThat(Selector::implementInterface(Decoder::class))
            ->build();
    }
}
