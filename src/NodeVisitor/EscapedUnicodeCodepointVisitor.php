<?php

namespace Sstalle\php7cc\NodeVisitor;

use PhpParser\Node;
use Sstalle\php7cc\CompatibilityViolation\Message;

class EscapedUnicodeCodepointVisitor extends AbstractVisitor
{
    const LEVEL = Message::LEVEL_ERROR;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Scalar\String_) {
            return;
        }

        $unquotedStringValue = null;
        if ($node->getAttribute('isDoubleQuoted')) {
            $unquotedStringValue = substr($node->getAttribute('originalValue'), 1, -1);
        } elseif ($node->getAttribute('isHereDoc')) {
            // Skip T_START_HEREDOC, T_END_HEREDOC
            $unquotedStringValue = '';
            foreach (range($node->getAttribute('startTokenPos') + 1, $node->getAttribute('endTokenPos') - 1) as $i) {
                $unquotedStringValue .= $this->tokenCollection->getTokenStringValueAt($i);
            }
        }

        if (!$unquotedStringValue) {
            return;
        }

        $matches = array();
        if (preg_match('/((?<!\\\\)\\\\u{.*})/', $unquotedStringValue, $matches)) {
            $this->addContextMessage(
                sprintf('Unicode codepoint escaping "%s" in a string', $matches[0]),
                $node
            );
        }
    }
}
