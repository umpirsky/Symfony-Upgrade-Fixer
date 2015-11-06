<?php

namespace Symfony\Upgrade\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Upgrade\Fixer\Iterator\FixerIterator;

class ReadmeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('readme')
            ->setDescription('Generates the README content')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixersContent = '';
        foreach (new FixerIterator() as $fixer) {
            $fixersContent .= sprintf('| %s | %s |', $fixer->getName(), $fixer->getDescription()).PHP_EOL;
        }

        $output->write(sprintf(
            file_get_contents(__DIR__.'/../../../../../README.tpl'),
            $fixersContent
        ));
    }
}
