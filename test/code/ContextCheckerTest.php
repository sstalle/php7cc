<?php

class ContextCheckerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_FILE_EXTENSION = '.test';

    /**
     * @dataProvider messageProvider
     */
    public function testMessages($name, $code, $expectedMessages)
    {
        $containerBuilder = new \Sstalle\php7cc\Infrastructure\ContainerBuilder();
        $container = $containerBuilder->buildContainer(new Symfony\Component\Console\Output\NullOutput());
        $contextChecker = $container['contextChecker'];
        $context = new \Sstalle\php7cc\CompatibilityViolation\StringContext($code, 'test');

        $contextChecker->checkContext($context);
        $expectedMessageCount = count($expectedMessages);
        $actualMessages = array_merge($context->getMessages(), $context->getErrors());
        $actualMessageCount = count($actualMessages);
        $this->assertEquals($expectedMessageCount, $actualMessageCount, $name);
        if ($expectedMessageCount == $actualMessageCount) {
            /** @var \Sstalle\php7cc\AbstractBaseMessage $message */
            foreach ($actualMessages as $i => $message) {
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

            $name = $this->canonicalize(array_shift($parts));
            if ($this->containsVersionConstraint($name)) {
                if (!$this->satisfiesVersionConstraint($name)) {
                    continue;
                }

                $name = $this->stripVersionConstraint($name);
            }

            $fullName = $name . ' (' . $fileName . ')';
            // multiple sections possible with always two forming a pair
            foreach (array_chunk($parts, 2) as $chunk) {
                $messages = array_filter(explode("\n", $this->canonicalize($chunk[1])));
                $tests[] = array($fullName, $chunk[0], $messages);
            }
        }

        return $tests;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function stripVersionConstraint($name)
    {
        $nameParts = explode("\n", $name);
        array_pop($nameParts);

        return implode("\n", $nameParts);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function containsVersionConstraint($name)
    {
        $nameParts = explode("\n", $name);

        return count($nameParts) > 1 && substr(end($nameParts), 0, 3) === 'PHP';
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function satisfiesVersionConstraint($name)
    {
        if ($this->containsVersionConstraint($name)) {
            $nameParts = explode("\n", $name);
            // last line contains version constraints
            $versionConstraints = array();
            preg_match_all(
                '/\\s+(<|lt|<=|le|>|gt|>=|ge|==|=|eq|!=|<>|ne)([a-zA-Z0-9\\.\\-]+)/',
                end($nameParts),
                $versionConstraints
            );

            if (!count(array_shift($versionConstraints))) {
                throw new \RuntimeException(
                    sprintf(
                        'Version constraint %s was specified for test suite "%s" but no constraints could be extracted',
                        end($nameParts),
                        $this->stripVersionConstraint($name)
                    )
                );
            };

            foreach (range(0, count($versionConstraints[0]) - 1) as $constraintIndex) {
                if (!version_compare(
                    PHP_VERSION,
                    $versionConstraints[1][$constraintIndex],
                    $versionConstraints[0][$constraintIndex]
                )) {
                    return false;
                }
            }
        }

        return true;
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
