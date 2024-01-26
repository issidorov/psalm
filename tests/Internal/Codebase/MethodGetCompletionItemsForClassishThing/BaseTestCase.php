<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

use Psalm\Codebase;
use Psalm\Context;
use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Internal\Provider\FakeFileProvider;
use Psalm\Internal\Provider\Providers;
use Psalm\Tests\Internal\Provider\FakeFileReferenceCacheProvider;
use Psalm\Tests\Internal\Provider\ParserInstanceCacheProvider;
use Psalm\Tests\Internal\Provider\ProjectCacheProvider;
use Psalm\Tests\TestCase;
use Psalm\Tests\TestConfig;

use function array_map;

abstract class BaseTestCase extends TestCase
{
    private Codebase $codebase;

    public function setUp(): void
    {
        parent::setUp();

        $this->file_provider = new FakeFileProvider();

        $config = new TestConfig();

        $providers = new Providers(
            $this->file_provider,
            new ParserInstanceCacheProvider(),
            null,
            null,
            new FakeFileReferenceCacheProvider(),
            new ProjectCacheProvider(),
        );

        $this->codebase = new Codebase($config, $providers);

        $this->project_analyzer = new ProjectAnalyzer(
            $config,
            $providers,
            null,
            [],
            1,
            null,
            $this->codebase,
        );

        $this->project_analyzer->setPhpVersion('7.3', 'tests');
        $this->project_analyzer->getCodebase()->store_node_types = true;

        $this->codebase->config->throw_exception = false;
    }

    /**
     * @return list<string>
     */
    protected function getCompletionLabels(string $content, string $class_name, string $gap): array
    {
        $this->addFile('somefile.php', $content);

        $this->analyzeFile('somefile.php', new Context());

        $items = $this->codebase->getCompletionItemsForClassishThing($class_name, $gap, true);

        return array_map(fn($item) => $item->label, $items);
    }

    /**
     * @return iterable<array-key, array{0: string}>
     */
    public function providerGaps(): iterable
    {
        return [
            'object-gap' => ['->'],
            'static-gap' => ['::'],
        ];
    }
}
