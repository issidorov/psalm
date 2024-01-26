<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Fat tests for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class FindingCompletionEntitiesTest extends BaseTestCase
{
    /**
     * @dataProvider providerGaps
     */
    public function testUseTrait(string $gap): void
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
            trait C {
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
            }

            class A {
                use C;
            }
        EOF;

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

    /**
     * @dataProvider providerGaps
     */
    public function testUseTraitWithAbstractClass(string $gap): void
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
            trait C {
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
            }

            abstract class A {
                use C;
            }
        EOF;

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

    /**
     * @dataProvider providerGaps
     */
    public function testClassWithExtends(string $gap): void
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

    /**
     * @dataProvider providerGaps
     */
    public function testAstractClassWithInterface(string $gap): void
    {
        $content = <<<'EOF'
            <?php
            namespace B;

            interface C {
                public      function    publicObjMethod();
                protected   function    protectedObjMethod();
            }

            abstract class A implements C {
                abstract    public      function    publicObjMethod();
                abstract    protected   function    protectedObjMethod();
            }
        EOF;

        $actual_labels = $this->getCompletionLabels($content, 'B\A', $gap);

        $expected_labels = [
            '->' => [
                'publicObjMethod',
                'protectedObjMethod',
            ],
            '::' => [],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }

    /**
     * @dataProvider providerGaps
     */
    public function testClassWithAnnotationMixin(string $gap): void
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

            /**
             * @mixin C
             */
            class A {
                
            }
        EOF;

        $actual_labels = $this->getCompletionLabels($content, 'B\A', $gap);

        $expected_labels = [
            '->' => [
                'magicObjProp1',
                'magicObjProp2',
                'magicObjMethod',

                'publicObjProp',

                'publicObjMethod',

                'publicStaticMethod',
            ],
            '::' => [],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }

    public function testResolveCollisionWithMixin(): void
    {
        $content = <<<'EOF'
            <?php
            namespace B;

            /** @mixin A */
            class C {
                public $myObjProp;
            }

            /** @mixin C */
            class A {}
        EOF;

        $actual_labels = $this->getCompletionLabels($content, 'B\A', '->');

        $expected_labels = [
            'myObjProp',
        ];

        $this->assertEqualsCanonicalizing($expected_labels, $actual_labels);
    }
}
