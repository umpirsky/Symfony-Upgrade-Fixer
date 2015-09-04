<?php

namespace Symfony\Upgrade\Test;

use Symfony\CS\FixerInterface;
use Symfony\CS\StdinFileInfo;
use Symfony\Upgrade\Fixer;
use Symfony\Upgrade\Fixer\ProgressBarFixer;
use Symfony\Upgrade\Fixer\PropertyAccessFixer;

class FixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFixer
     */
    public function testThatRegisterBuiltInFixers(Fixer $fixer)
    {
        $this->assertCount(0, $fixer->getFixers());
        $fixer->registerBuiltInFixers();
        $this->assertGreaterThan(0, count($fixer->getFixers()));
    }

    /**
     * @dataProvider provideFixer
     */
    public function testThatCanAddAndGetFixers(Fixer $fixer)
    {
        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $fixer->addFixer($f1);
        $fixer->addFixer($f2);

        $this->assertTrue(in_array($f1, $fixer->getFixers(), true));
        $this->assertTrue(in_array($f2, $fixer->getFixers(), true));
    }

    /**
     * @dataProvider provideFixer
     */
    public function testThatFixSuccessfully(Fixer $fixer)
    {
        $fixer->addFixer(new ProgressBarFixer());
        $fixer->addFixer(new PropertyAccessFixer());

        $changed = $fixer->fix(true);

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$this->getPath()]);
    }

    /**
     * @dataProvider provideFixersDescriptionConsistencyCases
     */
    public function testFixersDescriptionConsistency(FixerInterface $fixer)
    {
        $this->assertRegExp('/^[A-Z@].*\.$/', $fixer->getDescription(), 'Description must start with capital letter or an @ and end with dot.');
    }

    public function provideFixer()
    {
        return [
            [new Fixer(new \ArrayIterator([new \SplFileInfo($this->getPath())]))],
        ];
    }

    public function provideFixersDescriptionConsistencyCases()
    {
        $fixer = new Fixer(new \ArrayIterator([new StdinFileInfo()]));
        $fixer->registerBuiltInFixers();
        $fixers = $fixer->getFixers();
        $cases = [];

        foreach ($fixers as $fixer) {
            $cases[] = [$fixer];
        }

        return $cases;
    }

    private function getPath()
    {
        return __DIR__.'/../Fixtures/Fixer/fixme.php';
    }
}
