<?php

namespace Psalm\Tests;

use Psalm\Tests\Traits\ValidCodeAnalysisTestTrait;

class MixinsDeepForPropertiesTest extends TestCase
{
    use ValidCodeAnalysisTestTrait;

    public function providerValidCodeParse(): iterable
    {
        return [
            'NamedMixinsWithoutT' => [
                'code' => <<<'PHP'
                    <?php
                    abstract class Foo {
                        public string $propString = 'hello';
                    }

                    /**
                     * @mixin Foo
                     */
                    abstract class Bar {
                        public int $propInt = 123;
                    }

                    /**
                     * @mixin Bar
                     */
                    class Baz {}

                    $baz = new Baz();
                    $a = $baz->propString;
                    $b = $baz->propInt;
                    PHP,
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
        ];
    }
}
