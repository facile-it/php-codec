<?php declare(strict_types=1);

use PhpAT\Rule\Rule;
use PhpAT\Selector\Selector;
use Pybatt\Codec\Codecs;
use Pybatt\Codec\Refine;

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

    public function testRefineClasses(): Rule
    {
        return $this->newRule
            ->classesThat(Selector::haveClassName('Pybatt\Codec\*\*Refine'))
            ->excludingClassesThat(Selector::haveClassName(Refine::class))
            ->mustImplement()
            ->classesThat(Selector::haveClassName(Refine::class))
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
}
