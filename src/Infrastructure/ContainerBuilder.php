<?php

namespace Sstalle\php7cc\Infrastructure;

use PhpParser\Parser;
use Sstalle\php7cc\CLIResultPrinter;
use Sstalle\php7cc\ContextChecker;
use Sstalle\php7cc\FileContextFactory;
use Sstalle\php7cc\Lexer\ExtendedLexer;
use Sstalle\php7cc\NodeStatementsRemover;
use Sstalle\php7cc\NodeTraverser\Traverser;
use Sstalle\php7cc\PathChecker;
use Sstalle\php7cc\PathCheckExecutor;
use Sstalle\php7cc\PathTraversableFactory;
use Symfony\Component\Console\Output\OutputInterface;
use PhpParser\PrettyPrinter\Standard as StandardPrettyPrinter;

class ContainerBuilder
{

    protected $checkerVisitors = array(
        'visitor.removedFunctionCall' => '\\Sstalle\\php7cc\\NodeVisitor\\RemovedFunctionCallVisitor',
        'visitor.reservedClassName' => '\\Sstalle\\php7cc\\NodeVisitor\\ReservedClassNameVisitor',
        'visitor.duplicateFunctionParameter' => '\\Sstalle\\php7cc\\NodeVisitor\\DuplicateFunctionParameterVisitor',
        'visitor.list' => '\\Sstalle\\php7cc\\NodeVisitor\\ListVisitor',
        'visitor.globalVariableVariable' => '\\Sstalle\\php7cc\\NodeVisitor\\GlobalVariableVariableVisitor',
        'visitor.indirectVariableOrMethodAccess' => '\\Sstalle\\php7cc\\NodeVisitor\\IndirectVariableOrMethodAccessVisitor',
        'visitor.funcGetArgs' => '\\Sstalle\\php7cc\\NodeVisitor\\FuncGetArgsVisitor',
        'visitor.foreach' => '\\Sstalle\\php7cc\\NodeVisitor\\ForeachVisitor',
        'visitor.invalidOctalLiteral' => '\\Sstalle\\php7cc\\NodeVisitor\\InvalidOctalLiteralVisitor',
        'visitor.hexadecimalNumberString' => '\\Sstalle\\php7cc\\NodeVisitor\\HexadecimalNumberStringVisitor',
        'visitor.escapedUnicodeCodepoint' => '\\Sstalle\\php7cc\\NodeVisitor\\EscapedUnicodeCodepointVisitor',
        'visitor.arrayOrObjectValueAssignmentByReference' => '\\Sstalle\\php7cc\\NodeVisitor\\ArrayOrObjectValueAssignmentByReferenceVisitor',
        'visitor.bitwiseShift' => '\\Sstalle\\php7cc\\NodeVisitor\\BitwiseShiftVisitor',
        'visitor.newAssignmentByReference' => '\\Sstalle\\php7cc\\NodeVisitor\\NewAssignmentByReferenceVisitor',
    );

    /**
     * @param OutputInterface $output
     * @return \Pimple
     */
    public function buildContainer(OutputInterface $output)
    {
        $container = new \Pimple();

        $container['lexer'] = $container->share(function() {
            return new ExtendedLexer(array(
                'usedAttributes' => array(
                    'comments',
                    'startLine',
                    'endLine',
                    'startTokenPos',
                    'endTokenPos',
                )
            ));
        });
        $container['parser'] = $container->share(function($c) {
            return new Parser($c['lexer']);
        });
        $visitors = $this->checkerVisitors;
        foreach ($visitors as $visitorServiceName => $visitorClass) {
            $container[$visitorServiceName] = $container->share(function() use ($visitorClass) {
                return new $visitorClass();
            });
        }
        $container['traverser'] = $container->share(function($c) use ($visitors) {
            $traverser = new Traverser();
            foreach (array_keys($visitors) as $visitorServiceName) {
                $traverser->addVisitor($c[$visitorServiceName]);
            }

            return $traverser;
        });

        $container['contextChecker'] = $container->share(function($c) {
            return new ContextChecker($c['parser'], $c['lexer'], $c['traverser']);
        });
        $container['output'] = $container->share(function() use ($output) {
            return new CLIOutputBridge($output);
        });
        $container['nodePrinter'] = $container->share(function() {
            return new StandardPrettyPrinter();
        });
        $container['resultPrinter'] = $container->share(function($c) {
            return new CLIResultPrinter($c['output'], $c['nodePrinter'], $c['nodeStatementsRemover']);
        });
        $container['pathChecker'] = $container->share(function($c) {
            return new PathChecker($c['contextChecker'], $c['fileContextFactory'], $c['resultPrinter']);
        });
        $container['nodeStatementsRemover'] = $container->share(function() {
            return new NodeStatementsRemover();
        });
        $container['fileContextFactory'] = $container->share(function() {
            return new FileContextFactory();
        });
        $container['pathTraversableFactory'] = $container->share(function () {
            return new PathTraversableFactory();
        });
        $container['pathCheckExecutor'] = $container->share(function ($c) {
            return new PathCheckExecutor($c['pathTraversableFactory'], $c['pathChecker']);
        });

        return $container;
    }

}