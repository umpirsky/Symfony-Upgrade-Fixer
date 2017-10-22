<?php

namespace Symfony\Upgrade\Test\Fixer;

class FormExtensionGetExtendedTypeFixerTest extends AbstractFixerTestBase
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
        ];
    }
}
