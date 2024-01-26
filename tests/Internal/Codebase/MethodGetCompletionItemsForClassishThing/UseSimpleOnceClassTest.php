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

    private function getAllProperties()
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

    private function getAllMethods()
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

    private function getExpectedCompletionLabels()
    {
        return [
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
    }

    /**
     * @dataProvider providerGaps
     */
    public function testFindingCompletionEntities(string $gap): void
    {
        $content = $this->getContent();

        $actual_labels = $this->getCompletionLabels($content, 'B\A', $gap);

        $expected_labels = $this->getExpectedCompletionLabels();

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }

    public function providerCompatibleBehaviors()
    {
        $completionLabels = $this->getExpectedCompletionLabels();
        $data = [];
        foreach ($this->getAllProperties() as $property) {
            $key = 'Object-gap with ' . $property;
            $isValid = in_array($property, $completionLabels['->']);
            $data[$key] = ["echo (new A)->$property;", $isValid];
        }
        foreach ($this->getAllMethods() as $method) {
            $key = 'Object-gap with ' . $method;
            $isValid = in_array($method, $completionLabels['->']);
            $data[$key] = ["(new A)->$method();", $isValid];
        }
        foreach ($this->getAllProperties() as $property) {
            $key = 'Static-gap with ' . $property;
            $isValid = in_array($property, $completionLabels['::']);
            $data[$key] = ["echo A::\$$property;", $isValid];
        }
        foreach ($this->getAllMethods() as $method) {
            $key = 'Static-gap with ' . $method;
            $isValid = in_array($method, $completionLabels['::']);
            $data[$key] = ["A::$method();", $isValid];
        }
        return $data;
    }

    /**
     * @dataProvider providerCompatibleBehaviors
     */
    public function testCompatibleBehaviors($addon, $expectedIsValid)
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

        if ($expectedIsValid) {
            $this->assertNull($error);
        } else {
            $this->assertNotNull($error);
        }
    }
}
