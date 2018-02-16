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
        return [
            $this->prepareTestCase($this->getCaseOutputFileName(1), 'case1-input.php'),
            $this->prepareTestCase($this->getCaseOutputFileName(2), 'case2-input.php'),
        ];
    }

    /**
     * @param int $caseNumber
     * @return string
     */
    private function getCaseOutputFileName($caseNumber)
    {
        return sprintf('case%d-output-php%s.php', $caseNumber, PHP_VERSION_ID > 50500 ? '55+' : '54');
    }
}
