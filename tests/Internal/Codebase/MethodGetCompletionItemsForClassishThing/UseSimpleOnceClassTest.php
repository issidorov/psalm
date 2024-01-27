<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseSimpleOnceClassTest extends BaseTestCase
{
    protected function getContent(): string
    {
        return <<<'EOF'
            <?php
            namespace B;

            /**
             * @property int $magicObjProp1
             * @property-read string $magicObjProp2
             * @method int magicObjMethod()
             * @method static string magicStaticMethod()
             * <<ADDON_ANNOTATION>>
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

                <<INNER_ADDON_CODE>>
            }

            <<OUTER_ADDON_CODE>>
        EOF;
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
