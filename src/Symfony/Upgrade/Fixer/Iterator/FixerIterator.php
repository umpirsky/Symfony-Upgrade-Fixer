<?php

namespace Symfony\Upgrade\Fixer\Iterator;

use Symfony\Component\Finder\Finder;

class FixerIterator implements \Iterator
{
    protected $fixers = [];

    public function __construct()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/..')->depth(0) as $file) {
            $class = 'Symfony\\Upgrade\\Fixer\\'.$file->getBasename('.php');

            if ((new \ReflectionClass($class))->isAbstract()) {
                continue;
            }

            $this->attach(new $class());
        }

        $this->rewind();
    }

    public function attach($fixer)
    {
        $this->fixers[] = $fixer;
    }

    public function rewind()
    {
        reset($this->fixers);
    }

    public function valid()
    {
        return false !== $this->current();
    }

    public function next()
    {
        next($this->fixers);
    }

    public function current()
    {
        return current($this->fixers);
    }

    public function key()
    {
        return key($this->fixers);
    }
}
