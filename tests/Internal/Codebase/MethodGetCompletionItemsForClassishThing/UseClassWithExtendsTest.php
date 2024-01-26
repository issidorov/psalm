<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseClassWithExtendsTest extends BaseTestCase
{
    private function getContent(): string
    {
        return <<<'EOF'
            <?php
            namespace B;

            /**
             * @property int $magicObjProp1
             * @property-read string $magicObjProp2
             * @method int magicObjMethod()
             * @method static string magicStaticMethod()
             */
            class C {
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
            }

            class A extends C {
                
            }
        EOF;
    }

    /**
     * @dataProvider providerGaps
     */
    public function testFindingCompletionEntities(string $gap): void
    {
        $content = $this->getContent();

        $actual_labels = $this->getCompletionLabels($content, 'B\A', $gap);

        $expected_labels = [
            '->' => [
                'magicObjProp1',
                'magicObjProp2',
    
                'magicObjMethod',
    
                'publicObjProp',
                'protectedObjProp',

                'publicObjMethod',
                'protectedObjMethod',

                'publicStaticMethod',
                'protectedStaticMethod',
            ],
            '::' => [
                'magicStaticMethod',
                'publicStaticProp',
                'protectedStaticProp',

                'publicStaticMethod',
                'protectedStaticMethod',
            ],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }
}
