<?php

namespace Symfony\Upgrade\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Upgrade\FixCommand;
use Symfony\Upgrade\Fixer;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Symfony Upgrade Fixer', Fixer::VERSION);

        $this->add(new FixCommand());
    }
}
