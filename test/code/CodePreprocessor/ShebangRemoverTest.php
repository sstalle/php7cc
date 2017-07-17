<?php

namespace code\CodePreprocessor;

use Sstalle\php7cc\CodePreprocessor\ShebangRemover;

class ShebangRemoverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShebangRemover
     */
    private $remover;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->remover = new ShebangRemover();
    }

    /**
     * @dataProvider removesShebangWhenItsPresentProvider
     */
    public function testRemovesShebangWhenItsPresent($codeToProcess, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->remover->preprocess($codeToProcess));
    }

    /**
     * @dataProvider leavesCodeWithoutShebangIntactProvider
     */
    public function testLeavesCodeWithoutShebangIntact($codeToProcess, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->remover->preprocess($codeToProcess));
    }

    public function removesShebangWhenItsPresentProvider()
    {
        return array(
            array(
                <<< EOC
#!/usr/bin/env php
<?php namespace Scripts;

echo "hello\n";
EOC
                ,
                <<< EOC
<?php namespace Scripts;

echo "hello\n";
EOC
            ),

            array(
                <<< EOC
#!/usr/bin/php
<?php namespace Scripts;

echo "hello\n";
EOC
            ,
                <<< EOC
<?php namespace Scripts;

echo "hello\n";
EOC
            ),

            array(
                <<< EOC
#!/usr/bin/php foo bar
<?php namespace Scripts;

echo "hello\n";
EOC
            ,
                <<< EOC
<?php namespace Scripts;

echo "hello\n";
EOC
            ),
        );
    }

    public function leavesCodeWithoutShebangIntactProvider()
    {
        $codeSamples = array(
            array(
                <<< EOC
<?php namespace Scripts;

echo "hello\n";
EOC
            ),

            array(
                <<< EOC
<?php namespace Scripts;

#!/usr/bin/php

echo "hello\n";
EOC
            ),

            array(
                <<< EOC
#foo
<?php

echo "hello\n";
EOC
            ),
        );

        foreach ($codeSamples as $i => $sample) {
            $codeSamples[$i][1] = $sample[0];
        }

        return $codeSamples;
    }
}
