<?php declare(strict_types=1);

use PhpAT\Rule\Rule;
use PhpAT\Selector\Selector;
use Pybatt\Codec\Codec;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\Decoder;
use Pybatt\Codec\Encoder;
use Pybatt\Codec\Internal\Experimental\ExperimentalMarker;
use Pybatt\Codec\Refiner;

class ArchitectureTest extends \PhpAT\Test\ArchitectureTest
{
    public function testAllTypesExtendsAbstractType(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('Pybatt\Codec\*Type'))
            ->excludingClassesThat(Selector::haveClassName(\Pybatt\Codec\Internal\Type::class))
            ->mustExtend()
            ->classesThat(Selector::haveClassName(\Pybatt\Codec\Internal\Type::class))
            ->build();
    }

    public function testRefineNamedClassesMustImplementsRefineInterface(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('Pybatt\Codec\*\*Refine'))
            ->excludingClassesThat(Selector::haveClassName(Refiner::class))
            ->mustImplement()
            ->classesThat(Selector::haveClassName(Refiner::class))
            ->build();
    }

    public function testCodecsShouldntBeUsed(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::havePath('Codec/*'))
            ->excludingClassesThat(Selector::haveClassName(Codecs::class))
            ->mustNotDependOn()
            ->classesThat(Selector::haveClassName(Codecs::class))
            ->build();
    }

    public function testAnyInternalClassShouldNotDependFromAnythingOutsideExceptDefinitionInterfaces(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::havePath('Codec/Internal/*'))
            ->mustNotDependOn()
            ->classesThat(Selector::havePath('Codec/*'))
            ->excludingClassesThat(Selector::havePath('Codec/Internal/*'))
            ->andExcludingClassesThat(Selector::havePath('Codec/Validation/*'))
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
            ->andClassesThat(Selector::extendClass(\Pybatt\Codec\Internal\Type::class))
            ->build();
    }
}
