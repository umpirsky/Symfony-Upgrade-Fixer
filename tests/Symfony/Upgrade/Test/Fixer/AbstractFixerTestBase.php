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
}
