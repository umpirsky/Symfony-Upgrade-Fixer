<?php

namespace Symfony\Upgrade\Test\Fixer;

class FormOptionNamesFixerTest extends AbstractFixerTestBase
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
}
