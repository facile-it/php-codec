<?php

declare(strict_types=1);

namespace ArchitectureAssertions\Facile\PhpCodec;

use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Encoder;
use Facile\PhpCodec\Internal\Type;
use Facile\PhpCodec\Refiner;
use PhpAT\Rule\Rule;
use PhpAT\Selector\Selector;

class ArchitectureTest extends \PhpAT\Test\ArchitectureTest
{
    public function testAllTypesExtendsAbstractType(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('*Type'))
            ->excludingClassesThat(Selector::haveClassName(Type::class))
            ->mustExtend()
            ->classesThat(Selector::haveClassName(Type::class))
            ->build();
    }

    public function testRefineNamedClassesMustImplementsRefineInterface(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('*Refine'))
            ->excludingClassesThat(Selector::haveClassName(Refiner::class))
            ->mustImplement()
            ->classesThat(Selector::haveClassName(Refiner::class))
            ->build();
    }

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
            ->andExcludingClassesThat(Selector::haveClassName(Refiner::class))
            ->andExcludingClassesThat(Selector::haveClassName(Decoder::class))
            ->andExcludingClassesThat(Selector::haveClassName(Encoder::class))
            ->andExcludingClassesThat(Selector::haveClassName(Codec::class))
            ->build();
    }

    public function testCodecsMustExposeEveryInteralCodec(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName(Codecs::class))
            ->mustDependOn()
            ->andClassesThat(Selector::extendClass(Type::class))
            ->build();
    }
}
