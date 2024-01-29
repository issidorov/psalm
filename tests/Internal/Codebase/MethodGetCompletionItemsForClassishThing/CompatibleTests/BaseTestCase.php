<?php

declare(strict_types=1);

namespace Psalm\Tests\Internal\Codebase\MethodGetCompletionItemsForClassishThing\CompatibleTests;

use Error;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\IssueBuffer;
use Psalm\Config;
use Psalm\Internal\Analyzer\ProjectAnalyzer;
use Psalm\Internal\Provider\FakeFileProvider;
use Psalm\Internal\Provider\Providers;
use Psalm\Tests\Internal\Provider\FakeFileReferenceCacheProvider;
use Psalm\Tests\Internal\Provider\ParserInstanceCacheProvider;
use Psalm\Tests\Internal\Provider\ProjectCacheProvider;
use Psalm\Tests\TestCase;
use Psalm\Tests\TestConfig;

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
        $this->codebase->config->setCustomErrorLevel('MissingParamType', Config::REPORT_SUPPRESS);
    }

    public function provider()
    {
        $res = [];
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'data_') === 0) {
                $res[$method] = $this->{$method}();
            }
        }
        return $res;
    }

    /**
     * @dataProvider provider
     */
    public function test(string $template, string $className, string $gap)
    {
        $content = $this->getContent($template, $gap);

        $this->addFile('somefile.php', $content);
        $this->analyzeFile('somefile.php', new Context());

        $labelsWithErrors = $this->getLabelsOnAnalyzedErrors();

        // Valid labels (without errors)
        $expectedLabels = array_diff($this->getAllLabels(), $labelsWithErrors);

        $items = $this->codebase->getCompletionItemsForClassishThing($className, $gap);
        $actual_labels = array_map(fn($item) => $item->label, $items);

        // Filter magic methods
        $actual_labels = array_filter($actual_labels, fn($label) => strpos($label, '__') !== 0);

        $this->assertEqualsCanonicalizing($expectedLabels, $actual_labels);
    }

    /**
     * @return list<string>
     */
    private function getLabelsOnAnalyzedErrors(): array
    {
        $issues = IssueBuffer::getIssuesDataForFile('somefile.php');

        // Checking issue types
        foreach ($issues as $issue) {
            if (in_array($issue->type, ['NonStaticSelfCall'])) {
                continue;
            }
            if (strpos($issue->type, 'Undefined') !== false) {
                continue;
            }
            throw new Error('Unsupported error "' . $issue->type . ': ' . $issue->message . '".');
        }

        $labels = [];
        foreach ($this->getAllLabels() as $label) {
            foreach ($issues as $issue) {
                if (strpos($issue->selected_text, $label) !== false) {
                    $labels[] = $label;
                    break;
                }
            }
        }
        return $labels;
    }

    private function getContent($template, $gap)
    {
        $classAnnotations = <<<'EOF'
             * @property string $magicObjProp
             * @method string magicObjMethod()
             * @method static string magicStaticMethod()
        EOF;

        $classBody = <<<'EOF'
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

                <<ADDON_TEST_METHOD>>
        EOF;

        $addonTestMethodsByGap['->'] = <<<'EOF'
                public function __test() {
                    echo (new self)->magicObjProp;
                    echo (new self)->publicObjProp;
                    echo (new self)->protectedObjProp;
                    echo (new self)->privateObjProp;
                    echo (new self)->publicStaticProp;
                    echo (new self)->protectedStaticProp;
                    echo (new self)->privateStaticProp;

                    (new self)->magicObjMethod();
                    (new self)->magicStaticMethod();
                    (new self)->publicObjMethod();
                    (new self)->protectedObjMethod();
                    (new self)->privateObjMethod();
                    (new self)->publicStaticMethod();
                    (new self)->protectedStaticMethod();
                    (new self)->privateStaticMethod();
                }
        EOF;

        $addonTestMethodsByGap['::'] = <<<'EOF'
                public static function __test() {
                    echo self::$magicObjProp;
                    echo self::$publicObjProp;
                    echo self::$protectedObjProp;
                    echo self::$privateObjProp;
                    echo self::$publicStaticProp;
                    echo self::$protectedStaticProp;
                    echo self::$privateStaticProp;

                    self::magicObjMethod();
                    self::magicStaticMethod();
                    self::publicObjMethod();
                    self::protectedObjMethod();
                    self::privateObjMethod();
                    self::publicStaticMethod();
                    self::protectedStaticMethod();
                    self::privateStaticMethod();
                }
        EOF;

        $content = str_replace('<<CLASS_ANNOTATION>>', trim($classAnnotations, "* \n\r\t\v\0"), $template);
        $content = str_replace('<<CLASS_BODY>>', trim($classBody), $content);
        $content = str_replace('<<ADDON_TEST_METHOD>>', trim($addonTestMethodsByGap[$gap]), $content);

        return $content;
    }

    private function getAllLabels()
    {
        return [
            'magicObjProp',
            'publicObjProp',
            'protectedObjProp',
            'privateObjProp',
            'publicStaticProp',
            'protectedStaticProp',
            'privateStaticProp',

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
}
