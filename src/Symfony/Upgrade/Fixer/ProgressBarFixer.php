<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class ProgressBarFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->fixUseDeclarations(
            $tokens,
            ['Symfony', 'Component', 'Console', 'Helper', 'ProgressHelper'],
            'ProgressBar'
        );

        if ($used) {
            $this->fixUsages($tokens);
        }

        return $tokens->generateCode();
    }

    private function fixUsages(Tokens $tokens)
    {
        $newTokens = $tokens->findSequence([
            [T_NEW],
            [T_STRING, 'ProgressHelper'],
       ]);

        if (null === $newTokens) {
            return;
        }

        $newTokensIndexes = array_keys($newTokens);

        $newTokens[$newTokensIndexes[count($newTokensIndexes) - 1]]
            ->setContent('ProgressBar')
        ;

        $this->fixUsages($tokens);
    }

    public function getDescription()
    {
        return 'ProgressHelper has been removed in favor of ProgressBar.';
    }
}
