<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class ProgressBarFixer extends RenameFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->renameUseDeclarations(
            $tokens,
            ['Symfony', 'Component', 'Console', 'Helper', 'ProgressHelper'],
            'ProgressBar'
        );

        if ($used) {
            $this->renameUsages($tokens, 'ProgressHelper', 'ProgressBar');
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'ProgressHelper has been removed in favor of ProgressBar.';
    }
}
