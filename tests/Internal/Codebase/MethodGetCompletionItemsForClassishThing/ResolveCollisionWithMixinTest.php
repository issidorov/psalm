<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Test for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class ResolveCollisionWithMixinTest extends BaseTestCase
{
    private function getContent(): string
    {
        return <<<'EOF'
            <?php
            namespace B;

            /** @mixin A */
            class C {
                public $myObjProp;
            }

            /** @mixin C */
            class A {}
        EOF;
    }

    public function testFindingCompletionEntities(): void
    {
        $content = $this->getContent();

        $actual_labels = $this->getCompletionLabels($content, 'B\A', '->');

        $expected_labels = [
            'myObjProp',
        ];

        $this->assertEqualsCanonicalizing($expected_labels, $actual_labels);
    }
}
