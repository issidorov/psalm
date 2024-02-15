<?php

namespace Psalm\Tests;

use Psalm\Tests\Traits\ValidCodeAnalysisTestTrait;

class MixinsDeepForStaticMethodsTest extends TestCase
{
    use ValidCodeAnalysisTestTrait;

    public function providerValidCodeParse(): iterable
    {
        return [
            'NamedMixinsWithoutT' => [
                'code' => '<?php
                    abstract class Foo {
                        public static function getString(): string {}
                    }

                    /**
                     * @mixin Foo
                     */
                    abstract class Bar {
                        public static function getInt(): int {}
                    }

                    /**
                     * @mixin Bar
                     */
                    class Baz {}

                    /** @mixin Baz */
                    class Bat {}
                    $a = Bat::getString();
                    $b = Bat::getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
                'ignored_issues' => ['InvalidReturnType'],
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
                        public static function getString() {}
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
                        public static function getInt() {}
                    }

                    /**
                     * @template T1
                     * @template T2
                     * @mixin Bar<T1, T2>
                     */
                    class Baz {}

                    /** @mixin Baz<string, int> */
                    class Bat {}
                    $a = Bat::getString();
                    $b = Bat::getInt();',
                'assertions' => [
                    '$a' => 'string',
                    '$b' => 'int',
                ],
                'ignored_issues' => ['InvalidReturnType'],
            ],
        ];
    }
}
