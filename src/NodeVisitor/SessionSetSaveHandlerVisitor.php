<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\NodeAnalyzer\FunctionAnalyzer;

class SessionSetSaveHandlerVisitor extends AbstractVisitor
{
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
    }

    public function enterNode(Node $node)
    {
        if ($this->functionAnalyzer->isFunctionCallByStaticName($node, array('session_set_save_handler' => true))) {
            $this->addContextMessage(
                'Check that callbacks that are passed to "session_set_save_handler" '
                . 'and return false or -1 (if any) operate correctly',
                $node
            );
        }
    }
}
