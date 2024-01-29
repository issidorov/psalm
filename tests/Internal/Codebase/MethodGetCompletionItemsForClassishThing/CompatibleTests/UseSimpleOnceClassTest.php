<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing\CompatibleTests;

class UseSimpleOnceClassTest extends BaseTestCase
{
    protected function data_withoutMagicMethods_withObjectGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                // public function __get($name) {}
                // public function __call($name, $attributes) {}
                // public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '->'];
    }

    protected function data_withoutMagicMethods_withStaticGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                // public function __get($name) {}
                // public function __call($name, $attributes) {}
                // public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '::'];
    }

    protected function data_withMethodGet_withObjectGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                public function __get($name) {}
                // public function __call($name, $attributes) {}
                // public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '->'];
    }

    protected function data_withMethodCall_withObjectGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                // public function __get($name) {}
                public function __call($name, $attributes) {}
                // public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '->'];
    }

    protected function data_withMethodStaticCall_withObjectGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                // public function __get($name) {}
                // public function __call($name, $attributes) {}
                public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '->'];
    }

    protected function data_withMethodStaticCall_withStaticGap()
    {
        $template = <<<'EOF'
            <?php
            namespace B;

            /**
             * <<CLASS_ANNOTATION>>
             */
            class A {
                <<CLASS_BODY>>

                // public function __get($name) {}
                // public function __call($name, $attributes) {}
                public static function __callStatic($name, $attributes) {}
            }
        EOF;
        
        return [$template, 'B\A', '::'];
    }
}
