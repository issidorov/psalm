<?php

namespace Psalm\Tests;

use Psalm\Tests\Traits\ValidCodeAnalysisTestTrait;

class MixinsDeepForObjectMethodsTest extends TestCase
{
    use ValidCodeAnalysisTestTrait;

    public function providerValidCodeParse(): iterable
    {
        return [
            'NamedMixinsWithoutT' => [
                'code' => '<?php
                    abstract class Foo {
                        abstract public function getString(): string;
                    }

                    /**
                     * @mixin Foo
                     */
                    abstract class Bar {
                        abstract public function getInt(): int;
                    }

                    /**
                     * @mixin Bar
                     */
                    class Baz {}

                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
            'NamedMixinsWithT' => [
                'code' => '<?php
                    /**
                     * @template T
                     */
                    abstract class Foo {
                        /**
                         * @return T
                         */
                        abstract public function getString();
                    }

                    /**
                     * @template T1
                     * @template T2
                     * @mixin Foo<T1>
                     */
                    abstract class Bar {
                        /**
                         * @return T2
                         */
                        abstract public function getInt();
                    }

                    /**
                     * @template T1
                     * @template T2
                     * @mixin Bar<T1, T2>
                     */
                    class Baz {}

                    /** @var Baz<string, int> */
                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
            'TemplatedMixins' => [
                'code' => '<?php
                    abstract class Foo {
                        abstract public function getString(): string;
                    }

                    /**
                     * @template T
                     * @mixin T
                     */
                    abstract class Bar {
                        abstract public function getInt(): int;
                    }

                    /**
                     * @template T
                     * @mixin T
                     */
                    class Baz {}

                    /** @var Baz<Bar<Foo>> */
                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
            'CombineNamedAndTemplatedMixins' => [
                'code' => '<?php
                    abstract class Foo {
                        abstract public function getString(): string;
                    }

                    /**
                     * @template T
                     * @mixin T
                     */
                    abstract class Bar {
                        abstract public function getInt(): int;
                    }

                    /**
                     * @template T
                     * @mixin Bar<T>
                     */
                    class Baz {}

                    /** @var Baz<Foo> */
                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
            'CombineTemplatedAndNamedMixinsWithoutT' => [
                'code' => '<?php
                    abstract class Foo {
                        abstract public function getString(): string;
                    }

                    /**
                     * @mixin Foo
                     */
                    abstract class Bar {
                        abstract public function getInt(): int;
                    }

                    /**
                     * @template T
                     * @mixin T
                     */
                    class Baz {}

                    /** @var Baz<Bar> $baz */
                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
            'CombineTemplatedAndNamedMixinsWithT' => [
                'code' => '<?php
                    /**
                     * @template T
                     */
                    abstract class Foo {
                        /**
                         * @return T
                         */
                        abstract public function getString();
                    }

                    /**
                     * @mixin Foo<string>
                     */
                    abstract class Bar {
                        abstract public function getInt(): int;
                    }

                    /**
                     * @template T
                     * @mixin T
                     */
                    class Baz {}

                    /** @var Baz<Bar> $baz */
                    $baz = new Baz();
                    $a = $baz->getString();
                    $b = $baz->getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
            ],
        ];
    }
}
