<?php

namespace Symfony\Upgrade\Fixer\Iterator;

use Symfony\Component\Finder\Finder;

class FixerIterator implements \IteratorAggregate
{
    protected $fixers = [];

    public function __construct()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/..')->sortByName()->depth(0) as $file) {
            $class = 'Symfony\\Upgrade\\Fixer\\'.$file->getBasename('.php');

            if ((new \ReflectionClass($class))->isAbstract()) {
                continue;
            }

            $this->fixers[] = new $class();
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fixers);
    }
}
