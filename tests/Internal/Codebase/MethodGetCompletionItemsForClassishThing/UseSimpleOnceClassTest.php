<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseSimpleOnceClassTest extends BaseTestCase
{
    private function getContent(string $innerAddon = '', string $outerAddon = ''): string
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

                <<INNER_ADDON>>
            }

            <<OUTER_ADDON>>
        EOF;

        $content = str_replace('<<INNER_ADDON>>', $innerAddon, $content);
        $content = str_replace('<<OUTER_ADDON>>', $outerAddon, $content);

        return $content;
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

                'publicObjMethod',
                'protectedObjMethod',
                'privateObjMethod',
                
                'publicStaticMethod',
                'protectedStaticMethod',
                'privateStaticMethod',
            ],
            '::' => [
                'magicStaticMethod',

                'publicStaticProp',
                'protectedStaticProp',
                'privateStaticProp',
    
                'publicStaticMethod',
                'protectedStaticMethod',
                'privateStaticMethod',
            ],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }

    public function dataCompatibleBehaviorsWithValid()
    {
        return [
            'Object-gap with magicObjProp1' => ['echo (new A)->magicObjProp1;'],
            'Object-gap with magicObjProp2' => ['echo (new A)->magicObjProp2;'],

            'Object-gap with magicObjMethod' => ['(new A)->magicObjMethod();'],

            'Object-gap with publicObjProp' => ['echo (new A)->publicObjProp;'],
            'Object-gap with protectedObjProp' => ['echo (new A)->protectedObjProp;'],
            'Object-gap with privateObjProp' => ['echo (new A)->privateObjProp;'],

            'Object-gap with publicObjMethod' => ['(new A)->publicObjMethod();'],
            'Object-gap with protectedObjMethod' => ['(new A)->protectedObjMethod();'],
            'Object-gap with privateObjMethod' => ['(new A())->privateObjMethod();'],
            
            'Object-gap with publicStaticMethod' => ['(new A)->publicStaticMethod();'],
            'Object-gap with protectedStaticMethod' => ['(new A)->protectedStaticMethod();'],
            'Object-gap with privateStaticMethod' => ['(new A)->privateStaticMethod();'],

            'Static-gap with magicStaticMethod' => ['A::magicStaticMethod();'],

            'Static-gap with publicStaticProp' => ['echo A::$publicStaticProp;'],
            'Static-gap with protectedStaticProp' => ['echo A::$protectedStaticProp;'],
            'Static-gap with privateStaticProp' => ['echo A::$privateStaticProp;'],

            'Static-gap with publicStaticMethod' => ['A::publicStaticMethod();'],
            'Static-gap with protectedStaticMethod' => ['A::protectedStaticMethod();'],
            'Static-gap with privateStaticMethod' => ['A::privateStaticMethod();'],
        ];
    }

    /**
     * @dataProvider dataCompatibleBehaviorsWithValid
     */
    public function testCompatibleBehaviorsWithValid($addon)
    {
        $innerAddon = <<<'EOF'
                public function __get(string $name) {}
                public function __call(string $name, array $arguments) {}
                public static function __callStatic(string $name, array $arguments) {}

                public function foo() {
                    <<ADDON>>
                }
        EOF;
        
        $innerAddon = str_replace('<<ADDON>>', $addon, $innerAddon);

        $content = $this->getContent($innerAddon);

        $error = $this->findFirstError($content);

        $this->assertNull($error);
    }
}
