<?php

namespace Symfony\Upgrade\Console\Command;

use Symfony\CS\ErrorsManager;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Upgrade\Fixer;

class FixCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDefinition(
                [
                    new InputArgument('path', InputArgument::REQUIRED),
                    new InputOption('dry-run', '', InputOption::VALUE_NONE, 'Only shows which files would have been modified'),
                ]
            )
            ->setDescription('Fixes a directory or a file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $errorsManager = new ErrorsManager();
        $stopwatch = new Stopwatch();

        if (is_file($path)) {
            $finder = new \ArrayIterator([new \SplFileInfo($path)]);
        } else {
            $finder = new DefaultFinder();
            $finder->setDir($path);
        }

        $this->fixer = new Fixer($finder, $errorsManager, $stopwatch);
        $this->fixer->registerBuiltInFixers();

        $stopwatch->start('fixFiles');

        $changed = $this->fixer->fix(
            $input->getOption('dry-run')
        );

        $stopwatch->stop('fixFiles');

        $verbosity = $output->getVerbosity();

        $i = 1;

        foreach ($changed as $file => $fixResult) {
            $output->write(sprintf('%4d) %s', $i++, $file));

            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult['appliedFixers'])));
            }

            $output->writeln('');
        }

        if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
            $output->writeln('Fixing time per file:');

            foreach ($stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $output->writeln(sprintf('[%.3f s] %s', $event->getDuration() / 1000, $file));
            }

            $output->writeln('');
        }

        $fixEvent = $stopwatch->getEvent('fixFiles');
        $output->writeln(sprintf('Fixed all files in %.3f seconds, %.3f MB memory used', $fixEvent->getDuration() / 1000, $fixEvent->getMemory() / 1024 / 1024));

        if (!$errorsManager->isEmpty()) {
            $output->writeLn('');
            $output->writeLn('Files that were not fixed due to internal error:');

            foreach ($errorsManager->getErrors() as $i => $error) {
                $output->writeLn(sprintf('%4d) %s', $i + 1, $error['filepath']));
            }
        }

        return empty($changed) ? 0 : 1;
    }
}
