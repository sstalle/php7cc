<?php

namespace Sstalle\php7cc;

use PhpParser\Parser;
use Sstalle\php7cc\CompatibilityViolation\ContextInterface;
use Sstalle\php7cc\CompatibilityViolation\FileContext;
use Sstalle\php7cc\Error\CheckError;
use Sstalle\php7cc\Lexer\ExtendedLexer;
use Sstalle\php7cc\NodeTraverser\Traverser;

class ContextChecker
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ExtendedLexer
     */
    protected $lexer;

    /**
     * @var Traverser
     */
    protected $traverser;

    /**
     * @param Parser        $parser
     * @param ExtendedLexer $lexer
     * @param Traverser     $traverser
     */
    public function __construct(Parser $parser, ExtendedLexer $lexer, Traverser $traverser)
    {
        $this->parser = $parser;
        $this->lexer = $lexer;
        $this->traverser = $traverser;
    }

    /**
     * @param ContextInterface $context
     *
     * @return FileContext
     */
    public function checkContext(ContextInterface $context)
    {
        try {
            $parsedStatements = $this->parser->parse($context->getCheckedCode());
            $this->traverser->traverse($parsedStatements, $context, $this->lexer->getTokens());
        } catch (\Exception $e) {
            $context->addError(new CheckError($e->getMessage()));
        } catch (\ParseException $e) {
            $context->addError(new CheckError($e->getMessage(), $e->getLine()));
        }
    }
}
