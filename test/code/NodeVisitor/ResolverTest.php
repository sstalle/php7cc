<?php

namespace code\NodeVisitor;

use Sstalle\php7cc\AbstractBaseMessage;
use Sstalle\php7cc\NodeVisitor\AbstractVisitor;
use Sstalle\php7cc\NodeVisitor\Resolver;
use Sstalle\php7cc\NodeVisitor\VisitorInterface;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testResolvesCorrectlyAccordingToLevelProvider
     *
     * @param VisitorInterface[] $visitors
     * @param $level
     */
    public function testResolvesCorrectlyAccordingToLevel($visitors, $level)
    {
        $resolver = new Resolver($visitors, $level);
        $expectedVisitors = array();
        foreach ($visitors as $visitor) {
            if ($visitor->getLevel() >= $level) {
                $expectedVisitors[] = $visitor;
            }
        }

        $this->assertSame($expectedVisitors, $resolver->resolve());
    }

    public function testResolvesCorrectlyAccordingToLevelProvider()
    {
        $data = array(
            array(array(), AbstractBaseMessage::LEVEL_INFO),
            array(array(AbstractBaseMessage::LEVEL_INFO, AbstractBaseMessage::LEVEL_INFO), AbstractBaseMessage::LEVEL_INFO),
            array(array(AbstractBaseMessage::LEVEL_INFO, AbstractBaseMessage::LEVEL_WARNING), AbstractBaseMessage::LEVEL_WARNING),
            array(array(AbstractBaseMessage::LEVEL_INFO, AbstractBaseMessage::LEVEL_WARNING, AbstractBaseMessage::LEVEL_ERROR, AbstractBaseMessage::LEVEL_WARNING), AbstractBaseMessage::LEVEL_ERROR),
            array(array(AbstractBaseMessage::LEVEL_INFO, AbstractBaseMessage::LEVEL_INFO), AbstractBaseMessage::LEVEL_ERROR),
        );

        foreach ($data as $i => $item) {
            $visitors = array();
            foreach ($item[0] as $level) {
                $visitors[] = new DummyVisitor($level);
            }

            $data[$i][0] = $visitors;
        }

        return $data;
    }
}

class DummyVisitor extends AbstractVisitor
{
    /**
     * @var int
     */
    protected $level;

    /**
     * @param int $level
     */
    public function __construct($level)
    {
        $this->level = $level;
    }

    public function getLevel()
    {
        return $this->level;
    }
}
