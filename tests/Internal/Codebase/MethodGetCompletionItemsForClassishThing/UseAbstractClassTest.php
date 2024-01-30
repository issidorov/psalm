<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseAbstractClassTest extends BaseTestCase
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
            abstract class A {
                public      $publicObjProp;
                protected   $protectedObjProp;
                private     $privateObjProp;

                public      static  $publicStaticProp;
                protected   static  $protectedStaticProp;
                private     static  $privateStaticProp;

                abstract    public      function    abstractPublicMethod();
                abstract    protected   function    abstractProtectedMethod();

                public      function    publicObjMethod() {}
                protected   function    protectedObjMethod() {}
                private     function    privateObjMethod() {}

                public      static  function    publicStaticMethod() {}
                protected   static  function    protectedStaticMethod() {}
                private     static  function    privateStaticMethod() {}

                public function __get($name) {}
                public function __call($name, $attributes) {}
                public static function __callStatic($name, $attributes) {}
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
                'privateObjProp',
    
                'abstractPublicMethod',
                'abstractProtectedMethod',

                'publicObjMethod',
                'protectedObjMethod',
                'privateObjMethod',
                
                'publicStaticMethod',
                'protectedStaticMethod',
                'privateStaticMethod',

                '__get',
                '__call',
                '__callStatic',
            ],
            '::' => [
                'magicStaticMethod',

                'publicStaticProp',
                'protectedStaticProp',
                'privateStaticProp',
    
                'publicStaticMethod',
                'protectedStaticMethod',
                'privateStaticMethod',

                '__callStatic',
            ],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }
}
