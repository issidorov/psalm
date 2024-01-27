<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing;

use Psalm\Codebase;
use Psalm\Context;
use Psalm\IssueBuffer;
use Psalm\Internal\Analyzer\ClassLikeAnalyzer;
use Psalm\Exception\CodeException;
use Psalm\Config;
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

        $this->codebase->config->setCustomErrorLevel('MissingReturnType', Config::REPORT_SUPPRESS);
        $this->codebase->config->setCustomErrorLevel('MissingPropertyType', Config::REPORT_SUPPRESS);
        $this->codebase->config->setCustomErrorLevel('MixedArgument', Config::REPORT_SUPPRESS);
    }

    abstract protected function getContent(string $innerAddon = '', string $outerAddon = ''): string;

    abstract protected function getAllProperties(): array;

    abstract protected function getAllMethods(): array;

    public function providerCompatible()
    {
        $data = [];
        foreach ($this->getAllProperties() as $property) {
            $key = 'Object-gap with ' . $property;
            $data[$key] = ['->', $property, "echo (new A)->$property;"];
        }
        foreach ($this->getAllMethods() as $method) {
            $key = 'Object-gap with ' . $method;
            $data[$key] = ['->', $method, "(new A)->$method();"];
        }
        foreach ($this->getAllProperties() as $property) {
            $key = 'Static-gap with ' . $property;
            $data[$key] = ['::', $property, "echo A::\$$property;"];
        }
        foreach ($this->getAllMethods() as $method) {
            $key = 'Static-gap with ' . $method;
            $data[$key] = ['::', $method, "A::$method();"];
        }
        return $data;
    }

    /**
     * @dataProvider providerCompatible
     */
    public function testCompatibleForInner(string $gap, string $label, string $addon)
    {
        $content = $this->getContent($addon);

        $this->addFile('somefile.php', $content);
        $this->analyzeFile('somefile.php', new Context());

        $has_errors = (count(IssueBuffer::getIssuesDataForFile('somefile.php')) > 0);

        $allow_visibilities = [
            ClassLikeAnalyzer::VISIBILITY_PUBLIC,
            ClassLikeAnalyzer::VISIBILITY_PROTECTED,
            ClassLikeAnalyzer::VISIBILITY_PRIVATE,
        ];
        $items = $this->codebase->getCompletionItemsForClassishThing('B\A', $gap, false, $allow_visibilities);
        $actual_labels = array_map(fn($item) => $item->label, $items);

        if ($has_errors) {
            $this->assertNotContains($label, $actual_labels);
        } else {
            $this->assertContains($label, $actual_labels);
        }
    }

    /**
     * @dataProvider providerCompatible
     */
    public function testCompatibleForOuter(string $gap, string $label, string $addon)
    {
        $content = $this->getContent('', $addon);

        $this->addFile('somefile.php', $content);
        $this->analyzeFile('somefile.php', new Context());

        $has_errors = (count(IssueBuffer::getIssuesDataForFile('somefile.php')) > 0);

        $allow_visibilities = [ClassLikeAnalyzer::VISIBILITY_PUBLIC];
        $items = $this->codebase->getCompletionItemsForClassishThing('B\A', $gap, false, $allow_visibilities);
        $actual_labels = array_map(fn($item) => $item->label, $items);

        if ($has_errors) {
            $this->assertNotContains($label, $actual_labels);
        } else {
            $this->assertContains($label, $actual_labels);
        }
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

    protected function findError(string $content): ?string
    {
        try {
            $this->addFile('somefile.php', $content);
            $this->analyzeFile('somefile.php', new Context());
            return null;
        } catch (CodeException $e) {
            return $e->getMessage();
        }
    }
}
