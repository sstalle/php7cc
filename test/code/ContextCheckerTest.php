<?php

class ContextCheckerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_FILE_EXTENSION = '.test';

    /**
     * @dataProvider messageProvider
     */
    public function testMessages($name, $code, $expectedMessages)
    {
        $lexer = new \Sstalle\php7cc\Lexer\ExtendedLexer(array(
            'usedAttributes' => array(
                'comments',
                'startLine',
                'endLine',
                'startTokenPos',
                'endTokenPos',
            ),
        ));
        $parser = new \PhpParser\Parser($lexer);
        $traverser = new \Sstalle\php7cc\NodeTraverser\Traverser(false);
        $visitors = array();
        foreach (array(
                     '\\Sstalle\\php7cc\\NodeVisitor\\RemovedFunctionCallVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\ReservedClassNameVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\DuplicateFunctionParameterVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\ListVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\GlobalVariableVariableVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\IndirectVariableOrMethodAccessVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\FuncGetArgsVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\ForeachVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\InvalidOctalLiteralVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\HexadecimalNumberStringVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\EscapedUnicodeCodepointVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\ArrayOrObjectValueAssignmentByReferenceVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\BitwiseShiftVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\NewAssignmentByReferenceVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\HTTPRawPostDataVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\YieldExpressionVisitor',
                     '\\Sstalle\\php7cc\\NodeVisitor\\YieldInExpressionContextVisitor',
                 ) as $visitorClass) {
            $visitors[] = new $visitorClass();
        }

        $visitors[] = new \Sstalle\php7cc\NodeVisitor\PregReplaceEvalVisitor(
            new \Sstalle\php7cc\Helper\RegExp\RegExpParser()
        );

        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }

        $contextChecker = new \Sstalle\php7cc\ContextChecker($parser, $lexer, $traverser);
        $context = new \Sstalle\php7cc\CompatibilityViolation\StringContext($code, 'test');
        $contextChecker->checkContext($context);
        $expectedMessageCount = count($expectedMessages);
        $actualMessageCount = count($context->getMessages());
        $this->assertEquals($expectedMessageCount, $actualMessageCount, $name);
        if ($expectedMessageCount == $actualMessageCount) {
            foreach ($context->getMessages() as $i => $message) {
                $this->assertEquals(
                    $this->canonicalize($expectedMessages[$i]),
                    $this->canonicalize($message->getRawText()),
                    $name
                );
            }
        }
    }

    /**
     * Copypasted from PhpParser\CodeTestAbstract.
     *
     * @return array
     */
    public function messageProvider()
    {
        $it = new \RecursiveDirectoryIterator(__DIR__ . '/../resource');
        $it = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new \RegexIterator($it, '(\.' . preg_quote('test') . '$)');
        $tests = array();
        foreach ($it as $file) {
            $fileName = realpath($file->getPathname());
            $fileContents = file_get_contents($fileName);
            // parse sections
            $fileContents = explode('-----', $fileContents);
            $parts = array_map(function ($i, $section) {
                return $i % 2 != 0 ? $section : trim($section);
            }, array_keys($fileContents), $fileContents);
            // first part is the name
            $name = array_shift($parts) . ' (' . $fileName . ')';
            // multiple sections possible with always two forming a pair
            foreach (array_chunk($parts, 2) as $chunk) {
                $messages = array_filter(explode("\n", $this->canonicalize($chunk[1])));
                $tests[] = array($name, $chunk[0], $messages);
            }
        }

        return $tests;
    }

    /**
     * Copypasted from PhpParser\CodeTestAbstract.
     *
     * @param $str string
     *
     * @return string
     */
    protected function canonicalize($str)
    {
        // trim from both sides
        $str = trim($str);
        // normalize EOL to \n
        $str = str_replace(array("\r\n", "\r"), "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }
}
