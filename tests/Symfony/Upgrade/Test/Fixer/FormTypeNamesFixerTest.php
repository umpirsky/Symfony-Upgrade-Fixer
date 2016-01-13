<?php

namespace Symfony\Upgrade\Test\Fixer;

class FormTypeNamesFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->makeTest($expected, $input, $file);
    }

    public function provideExamples()
    {
        $outputFile = PHP_VERSION_ID > 50500 ? 'case1-output-php55+.php' : 'case1-output-php54.php';

        return [
            $this->prepareTestCase($outputFile, 'case1-input.php'),
        ];
    }
}
