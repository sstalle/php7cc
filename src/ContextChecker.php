<?php

namespace Sstalle\php7cc;

use PhpParser\Parser;
use Sstalle\php7cc\CodePreprocessor\PreprocessorInterface;
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
     * @var PreprocessorInterface
     */
    protected $preprocessor;

    /**
     * @param Parser                $parser
     * @param ExtendedLexer         $lexer
     * @param Traverser             $traverser
     * @param PreprocessorInterface $preprocessor
     */
    public function __construct(
        Parser $parser,
        ExtendedLexer $lexer,
        Traverser $traverser,
        PreprocessorInterface $preprocessor
    ) {
        $this->parser = $parser;
        $this->lexer = $lexer;
        $this->traverser = $traverser;
        $this->preprocessor = $preprocessor;
    }

    /**
     * @param ContextInterface $context
     *
     * @return FileContext
     */
    public function checkContext(ContextInterface $context)
    {
        try {
            $checkedCode = $this->preprocessor->preprocess($context->getCheckedCode());
            $parsedStatements = $this->parser->parse($checkedCode);
            $this->traverser->traverse($parsedStatements, $context, $this->lexer->getTokens());
        } catch (\Exception $e) {
            $context->addError(new CheckError($e->getMessage()));
        } catch (\ParseException $e) {
            $context->addError(new CheckError($e->getMessage(), $e->getLine()));
        }
    }
}
