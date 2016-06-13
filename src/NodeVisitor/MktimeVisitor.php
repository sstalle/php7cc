<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class MktimeVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * @var string[]
     */
    protected $mktimeFamilyFunctions = array('mktime', 'gmmktime');

    /**
     * @var FunctionAnalyzer
     */
    protected $functionAnalyzer;

    /**
     * @param FunctionAnalyzer $functionAnalyzer
     */
    public function __construct(FunctionAnalyzer $functionAnalyzer)
    {
        $this->functionAnalyzer = $functionAnalyzer;
        $this->mktimeFamilyFunctions = array_flip($this->mktimeFamilyFunctions);
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$this->functionAnalyzer->isFunctionCallByStaticName($node, $this->mktimeFamilyFunctions)
            || count($node->args) < 7
        ) {
            return;
        }

        $this->addContextMessage(
            sprintf('Removed argument $is_dst used for function "%s"', $node->name->__toString()),
            $node
        );
    }
}
