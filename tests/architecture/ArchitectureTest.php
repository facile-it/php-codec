<?php declare(strict_types=1);

use PhpAT\Rule\Rule;
use PhpAT\Selector\Selector;
use Facile\Codec\Codec;
use Facile\Codec\Codecs;
use Facile\Codec\Decoder;
use Facile\Codec\Encoder;
use Facile\Codec\Internal\Experimental\ExperimentalMarker;
use Facile\Codec\Refiner;

class ArchitectureTest extends \PhpAT\Test\ArchitectureTest
{
    public function testAllTypesExtendsAbstractType(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('*Type'))
            ->excludingClassesThat(Selector::haveClassName(\Facile\Codec\Internal\Type::class))
            ->mustExtend()
            ->classesThat(Selector::haveClassName(\Facile\Codec\Internal\Type::class))
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
            ->andClassesThat(Selector::extendClass(\Facile\Codec\Internal\Type::class))
            ->build();
    }
}
