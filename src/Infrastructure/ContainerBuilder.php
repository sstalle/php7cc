<?php

namespace Sstalle\php7cc\Infrastructure;

use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Pimple\Container;
use Sstalle\php7cc\CLIResultPrinter;
use Sstalle\php7cc\ContextChecker;
use Sstalle\php7cc\CodePreprocessor\ShebangRemover;
use Sstalle\php7cc\ExcludedPathCanonicalizer;
use Sstalle\php7cc\Helper\OSDetector;
use Sstalle\php7cc\Helper\Path\PathHelperFactory;
use Sstalle\php7cc\Helper\RegExp\RegExpParser;
use Sstalle\php7cc\JsonResultPrinter;
use Sstalle\php7cc\Lexer\ExtendedLexer;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;
use Sstalle\php7cc\NodeStatementsRemover;
use Sstalle\php7cc\NodeTraverser\Traverser;
use Sstalle\php7cc\NodeVisitor\Resolver;
use Sstalle\php7cc\PathChecker;
use Sstalle\php7cc\PathCheckExecutor;
use Sstalle\php7cc\PathTraversableFactory;
use Sstalle\php7cc\ResultPrinterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpParser\PrettyPrinter\Standard as StandardPrettyPrinter;

class ContainerBuilder
{
    const BITWISE_SHIFT_VISITOR_ID = 'visitor.bitwiseShift';

    /**
     * @var array
     */
    protected $checkerVisitors = array(
        'visitor.removedFunctionCall' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\RemovedFunctionCallVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.reservedClassName' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\ReservedClassNameVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.duplicateFunctionParameter' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\DuplicateFunctionParameterVisitor',
        ),
        'visitor.list' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\ListVisitor',
        ),
        'visitor.globalVariableVariable' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\GlobalVariableVariableVisitor',
        ),
        'visitor.indirectVariableOrMethodAccess' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\IndirectVariableOrMethodAccessVisitor',
        ),
        'visitor.funcGetArgs' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\FuncGetArgsVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.continueBreakOutsideLoop' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\ContinueBreakOutsideLoopVisitor',
        ),
        'visitor.foreach' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\ForeachVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.invalidOctalLiteral' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\InvalidOctalLiteralVisitor',
        ),
        'visitor.hexadecimalNumberString' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\HexadecimalNumberStringVisitor',
        ),
        'visitor.escapedUnicodeCodepoint' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\EscapedUnicodeCodepointVisitor',
        ),
        'visitor.arrayOrObjectValueAssignmentByReference' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\ArrayOrObjectValueAssignmentByReferenceVisitor',
        ),
        self::BITWISE_SHIFT_VISITOR_ID => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\BitwiseShiftVisitor',
            'arguments' => array(),
        ),
        'visitor.newAssignmentByReference' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\NewAssignmentByReferenceVisitor',
        ),
        'visitor.httpRawPostData' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\HTTPRawPostDataVisitor',
        ),
        'visitor.pregReplaceEval' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\PregReplaceEvalVisitor',
            'dependencies' => array('regExpParser', 'nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.yieldExpression' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\YieldExpressionVisitor',
        ),
        'visitor.yieldInExpressionContext' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\YieldInExpressionContextVisitor',
        ),
        'visitor.mktime' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\MktimeVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.multipleSwitchDefaults' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\MultipleSwitchDefaultsVisitor',
        ),
        'visitor.passwordHashSalt' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\PasswordHashSaltVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.newClass' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\NewClassVisitor',
        ),
        'visitor.php4Constructor' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\PHP4ConstructorVisitor',
        ),
        'visitor.namespacedNewFunction' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\NamespacedNewFunctionVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.globalNewFunction' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\GlobalNewFunctionVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.divisionModuloByZero' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\DivisionModuloByZeroVisitor',
        ),
        'visitor.sessionSetSaveHandler' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\SessionSetSaveHandlerVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
        'visitor.setcookieEmptyName' => array(
            'class' => '\\Sstalle\\php7cc\\NodeVisitor\\SetcookieEmptyNameVisitor',
            'dependencies' => array('nodeAnalyzer.functionAnalyzer'),
        ),
    );

    /**
     * @var string
     */
    private $outputFormat;

    /**
     * @param OutputInterface $output
     * @param int             $intSize
     *
     * @return Container
     */
    public function buildContainer(OutputInterface $output, $intSize)
    {
        $this->checkerVisitors[static::BITWISE_SHIFT_VISITOR_ID]['arguments'][] = $intSize;

        $container = new Container();

        $container['lexer'] = function () {
            return new ExtendedLexer(array(
                'usedAttributes' => array(
                    'startLine',
                    'endLine',
                    'startTokenPos',
                    'endTokenPos',
                ),
            ));
        };
        $container['parser'] = function ($c) {
            return new Parser($c['lexer']);
        };

        $this->addVisitors($container);

        $visitors = $this->checkerVisitors;
        $container['traverser'] = function () {
            $traverser = new Traverser(false);
            // Resolve fully qualified name (class, interface, function, etc) to ease some process
            $traverser->addVisitor(new NameResolver());

            return $traverser;
        };

        $container['nodeVisitorResolver'] = function ($c) use ($visitors) {
            $visitorInstances = array();
            foreach (array_keys($visitors) as $visitorServiceName) {
                $visitorInstances[] = $c[$visitorServiceName];
            }

            return new Resolver($visitorInstances);
        };
        $container['nodeAnalyzer.functionAnalyzer'] = function () {
            return new FunctionAnalyzer();
        };
        $container['codePreprocessor.shebangRemover'] = function () {
            return new ShebangRemover();
        };
        $container['contextChecker'] = function ($c) {
            return new ContextChecker(
                $c['parser'],
                $c['lexer'],
                $c['traverser'],
                $c['codePreprocessor.shebangRemover']
            );
        };
        $container['output'] = function () use ($output) {
            return new CLIOutputBridge($output);
        };
        $container['nodePrinter'] = function () {
            return new StandardPrettyPrinter();
        };

        $outputFormat = $this->outputFormat;
        $container['resultPrinter'] = function ($c) use ($outputFormat) {
            switch ($outputFormat) {
                case ResultPrinterInterface::PLAIN_FORMAT:
                    return new CLIResultPrinter($c['output'], $c['nodePrinter'], $c['nodeStatementsRemover']);
                case ResultPrinterInterface::JSON_FORMAT:
                    return new JsonResultPrinter($c['output']);
                default: throw new \InvalidArgumentException('Invalid output format: ' . $outputFormat);
            }
        };

        $container['pathChecker'] = function ($c) {
            return new PathChecker($c['contextChecker'], $c['resultPrinter']);
        };
        $container['nodeStatementsRemover'] = function () {
            return new NodeStatementsRemover();
        };
        $container['pathTraversableFactory'] = function ($c) {
            return new PathTraversableFactory($c['excludedPathCanonicalizer']);
        };
        $container['pathCheckExecutor'] = function ($c) {
            return new PathCheckExecutor($c['pathTraversableFactory'], $c['pathChecker'], $c['traverser'], $c['nodeVisitorResolver']);
        };
        $container['excludedPathCanonicalizer'] = function ($c) {
            return new ExcludedPathCanonicalizer($c['pathHelper']);
        };
        $container['osDetector'] = function () {
            return new OSDetector();
        };
        $container['pathHelperFactory'] = function ($c) {
            return new PathHelperFactory($c['osDetector']);
        };
        $container['pathHelper'] = function ($c) {
            /** @var PathHelperFactory $pathHelperFactory */
            $pathHelperFactory = $c['pathHelperFactory'];

            return $pathHelperFactory->createPathHelper();
        };
        $container['regExpParser'] = function () {
            return new RegExpParser();
        };

        return $container;
    }

    /**
     * @param Container $container
     */
    protected function addVisitors(Container $container)
    {
        foreach ($this->checkerVisitors as $visitorServiceName => $visitorParameters) {
            $container[$visitorServiceName] = function ($c) use ($visitorParameters) {
                $visitorDependencyServiceNames = isset($visitorParameters['dependencies'])
                    ? $visitorParameters['dependencies']
                    : array();
                $otherVisitorArguments = isset($visitorParameters['arguments'])
                    ? $visitorParameters['arguments']
                    : array();
                $visitorClassName = $visitorParameters['class'];
                if (!$visitorDependencyServiceNames && !$otherVisitorArguments) {
                    /* This early return is required, because a ReflectionException is thrown
                     * from ReflectionClass::newInstanceArgs for classes without constructors
                     * on PHP 5.3.3
                     */
                    return new $visitorClassName();
                }

                $visitorClassReflection = new \ReflectionClass($visitorClassName);
                $visitorDependencies = array();
                foreach ($visitorDependencyServiceNames as $serviceName) {
                    $visitorDependencies[] = $c[$serviceName];
                }

                return $visitorClassReflection->newInstanceArgs(
                    array_merge($visitorDependencies, $otherVisitorArguments)
                );
            };
        }
    }

    /**
     * @param string $outputFormat
     *
     * @return ContainerBuilder
     */
    public function withOutputFormat($outputFormat)
    {
        $this->outputFormat = $outputFormat;

        return $this;
    }
}
