<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase as BaseAbstractFixerTestBase;

abstract class AbstractFixerTestBase extends BaseAbstractFixerTestBase
{
    protected function getFixer()
    {
        $name = 'Symfony\Upgrade\Fixer'.substr(get_called_class(), strlen(__NAMESPACE__), -strlen('Test'));

        return new $name();
    }

    protected function prepareTestCase($expectedFilename, $inputFilename = null)
    {
        $dir = __DIR__.'/../../Fixtures/Fixer/'.str_replace(
            '-fixer-test',
            '',
            strtolower(preg_replace('~(?<=\\w)([A-Z])~', '-$1', (new \ReflectionClass($this))->getShortName()))
        );

        $expectedFile = $this->getTestFile($dir.'/'.$expectedFilename);
        $inputFile = $inputFilename ? $this->getTestFile($dir.'/'.$inputFilename) : null;

        return [
            file_get_contents($expectedFile->getRealpath()),
            $inputFile ? file_get_contents($inputFile->getRealpath()) : null,
            $inputFile ?: $expectedFile,
        ];
    }
}
