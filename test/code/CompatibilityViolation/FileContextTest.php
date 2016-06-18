<?php

namespace code\CompatibilityViolation;

use Sstalle\php7cc\CompatibilityViolation\FileContext;
use Sstalle\php7cc\CompatibilityViolation\Message;
use Sstalle\php7cc\Error\CheckError;
use Symfony\Component\Finder\SplFileInfo as BaseSplFileInfo;

class FileContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testGetCheckedResourceNameProvider
     */
    public function testGetCheckedResourceName($fullPath, $relativePath, $useRelativePaths)
    {
        $file = new SplFileInfo($fullPath, null, $relativePath, '');
        $context = new FileContext($file, $useRelativePaths);

        $this->assertSame(
            $useRelativePaths ? $file->getRelativePathname() : $file->getRealPath(),
            $context->getCheckedResourceName()
        );
    }

    /**
     * @dataProvider testGetCheckedCodeProvider
     */
    public function testGetCheckedCode($fileContent)
    {
        $file = new SplFileInfo('/test.php', null, false, $fileContent);
        $context = new FileContext($file, false);

        $this->assertSame($fileContent, $context->getCheckedCode());
    }

    public function testHasMessageOrErrors()
    {
        $file = new SplFileInfo('/test.php', null, false, '');
        $context = new FileContext($file, false);

        $this->assertSame(false, $context->hasMessagesOrErrors());

        $context->addError(new CheckError('test'));
        $this->assertSame(true, $context->hasMessagesOrErrors());

        $context = new FileContext($file, false);
        $context->addMessage(new Message('test'));
        $this->assertSame(true, $context->hasMessagesOrErrors());
    }

    /**
     * @return array
     */
    public function testGetCheckedResourceNameProvider()
    {
        return array(
            array('/foo/bar.php', 'bar.php', true),
            array('C:\baz.php', 'test\bar.php', true),
            array('/foo/bar/baz.php', 'test/bar.php', false),
            array('C:\bar\baz.php', 'bar.php', false),
        );
    }

    /**
     * @return array
     */
    public function testGetCheckedCodeProvider()
    {
        return array(
            array('test'),
            array('<?php $foo = 1;'),
            array(''),
        );
    }
}

class SplFileInfo extends BaseSplFileInfo
{
    /**
     * @var string
     */
    protected $fullPath;

    /**
     * @var string
     */
    protected $content;

    /**
     * {@inheritdoc}
     */
    public function __construct($file, $relativePath, $relativePathname, $content)
    {
        parent::__construct($file, $relativePath, $relativePathname);
        $this->fullPath = $file;
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPath()
    {
        return $this->fullPath;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->content;
    }
}
