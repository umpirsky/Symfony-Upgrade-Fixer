<?php

namespace Symfony\Upgrade;

use Symfony\CS\ErrorsManager;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Upgrade\Fixer\Iterator\FixerIterator;

class Fixer
{
    const VERSION = '0.1';

    private $fixers = [];
    private $finder;
    private $errorsManager;
    private $stopwatch;

    public function __construct(
        \Traversable $finder,
        ErrorsManager $errorsManager = null,
        Stopwatch $stopwatch = null
    ) {
        $this->finder = $finder;
        $this->errorsManager = $errorsManager;
        $this->stopwatch = $stopwatch;
    }

    public function registerBuiltInFixers()
    {
        foreach (new FixerIterator() as $fixer) {
            $this->addFixer($fixer);
        }
    }

    public function addFixer(FixerInterface $fixer)
    {
        $this->fixers[] = $fixer;
    }

    public function getFixers()
    {
        return $this->fixers;
    }

    public function fix($dryRun = false)
    {
        $changed = [];

        if ($this->stopwatch) {
            $this->stopwatch->openSection();
        }

        foreach ($this->finder as $file) {
            if ($file->isDir() || $file->isLink()) {
                continue;
            }

            if ($this->stopwatch) {
                $this->stopwatch->start($this->getFileRelativePathname($file));
            }

            if ($fixInfo = $this->fixFile($file, $dryRun)) {
                $changed[$this->getFileRelativePathname($file)] = $fixInfo;
            }

            if ($this->stopwatch) {
                $this->stopwatch->stop($this->getFileRelativePathname($file));
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stopSection('fixFile');
        }

        return $changed;
    }

    private function fixFile(\SplFileInfo $file, $dryRun)
    {
        $new = $old = file_get_contents($file->getRealpath());

        $appliedFixers = [];

        Tokens::clearCache();

        try {
            foreach ($this->fixers as $fixer) {
                if (!$fixer->supports($file)) {
                    continue;
                }

                $newest = $fixer->fix($file, $new);
                if ($newest !== $new) {
                    $appliedFixers[] = $fixer->getName();
                }
                $new = $newest;
            }
        } catch (\Exception $e) {
            if ($this->errorsManager) {
                $this->errorsManager->report(ErrorsManager::ERROR_TYPE_EXCEPTION, $this->getFileRelativePathname($file), $e->__toString());
            }

            return;
        }

        if ($new !== $old) {
            if (!$dryRun) {
                file_put_contents($file->getRealpath(), $new);
            }
        }

        return $appliedFixers;
    }

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof FinderSplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
    }
}
