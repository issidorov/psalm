<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class UseAbstractClassWithInterfaceTest extends BaseTestCase
{
    private function getContent(): string
    {
        return <<<'EOF'
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
                'publicObjMethod',
                'protectedObjMethod',
            ],
            '::' => [],
        ];

        $this->assertEqualsCanonicalizing($expected_labels[$gap], $actual_labels);
    }
}
