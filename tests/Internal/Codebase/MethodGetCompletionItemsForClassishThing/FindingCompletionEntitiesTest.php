<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

/**
 * Fat tests for method `getCompletionItemsForClassishThing` of class `Psalm\Codebase`.
 */
final class FindingCompletionEntitiesTest extends BaseTestCase
{
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
