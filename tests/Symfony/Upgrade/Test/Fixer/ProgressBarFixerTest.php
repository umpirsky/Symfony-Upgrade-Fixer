<?php

namespace Symfony\Upgrade\Test\Fixer;

class ProgressBarFixerTest extends AbstractFixerTestBase
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
        return [
            $this->prepareTestCase('case1-output.php', 'case1-input.php'),
            $this->prepareTestCase('case2-output.php', 'case2-input.php'),
        ];
    }

    private function prepareTestCase($expectedFilename, $inputFilename = null)
    {
        $expectedFile = $this->getTestFile(__DIR__.'/../../Fixtures/Fixer/progress-bar/'.$expectedFilename);
        $inputFile = $inputFilename ? $this->getTestFile(__DIR__.'/../../Fixtures/Fixer/progress-bar/'.$inputFilename) : null;

        return [
            file_get_contents($expectedFile->getRealpath()),
            $inputFile ? file_get_contents($inputFile->getRealpath()) : null,
            $inputFile ?: $expectedFile,
        ];
    }
}
