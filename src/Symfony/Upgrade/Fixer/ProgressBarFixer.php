<?php

namespace Symfony\Upgrade\Fixer;

class ProgressBarFixer extends RenameClassFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        return $this->rename(
            $content,
            ['Symfony', 'Component', 'Console', 'Helper', 'ProgressHelper'],
            'ProgressBar'
        );
    }

    public function getDescription()
    {
        return 'ProgressHelper has been removed in favor of ProgressBar.';
    }
}
