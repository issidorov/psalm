<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseSimpleOnceClassTest extends BaseTestCase
{
    protected function getContent(string $innerAddon = '', string $outerAddon = ''): string
    {
        $content = <<<'EOF'
            <?php
            namespace B;

            /**
             * @property int $magicObjProp1
             * @property-read string $magicObjProp2
             * @method int magicObjMethod()
             * @method static string magicStaticMethod()
             */
            class A {
                public      $publicObjProp;
                protected   $protectedObjProp;
                private     $privateObjProp;

                public      static  $publicStaticProp;
                protected   static  $protectedStaticProp;
                private     static  $privateStaticProp;

                public      function    publicObjMethod() {}
                protected   function    protectedObjMethod() {}
                private     function    privateObjMethod() {}

                public      static  function    publicStaticMethod() {}
                protected   static  function    protectedStaticMethod() {}
                private     static  function    privateStaticMethod() {}

                public function __get(string $name) {}
                public function __call(string $name, array $arguments) {}
                public static function __callStatic(string $name, array $arguments) {}

                public function foo() {
                    <<INNER_ADDON>>
                }
            }

            <<OUTER_ADDON>>
        EOF;

        $content = str_replace('<<INNER_ADDON>>', $innerAddon, $content);
        $content = str_replace('<<OUTER_ADDON>>', $outerAddon, $content);

        return $content;
    }

    protected function getAllProperties(): array
    {
        return [
            'magicObjProp1',
            'magicObjProp2',
            'publicObjProp',
            'protectedObjProp',
            'privateObjProp',
            'publicStaticProp',
            'protectedStaticProp',
            'privateStaticProp',
        ];
    }

    protected function getAllMethods(): array
    {
        return [
            'magicObjMethod',
            'magicStaticMethod',
            'publicObjMethod',
            'protectedObjMethod',
            'privateObjMethod',
            'publicStaticMethod',
            'protectedStaticMethod',
            'privateStaticMethod',
        ];
    }
}
