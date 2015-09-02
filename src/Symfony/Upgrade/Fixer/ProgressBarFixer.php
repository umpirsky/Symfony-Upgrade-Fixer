<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class ProgressBarFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->fixUseDeclarations($tokens);

        if ($used) {
            $this->fixUsages($tokens);
        }

        return $tokens->generateCode();
    }

    private function fixUseDeclarations(Tokens $tokens)
    {
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Console'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Helper'],
            [T_NS_SEPARATOR],
            [T_STRING, 'ProgressHelper'],
            ';',
        ]);

        if (null === $useTokens) {
            return false;
        }

        $useTokensIndexes = array_keys($useTokens);

        $classNameToken = $useTokens[$useTokensIndexes[count($useTokensIndexes) - 2]];
        $classNameToken->setContent('ProgressBar');

        return true;
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

        $classNameToken = $newTokens[$newTokensIndexes[count($newTokensIndexes) - 1]];
        $classNameToken->setContent('ProgressBar');

        $this->fixUsages($tokens);
    }

    public function getDescription()
    {
        return 'ProgressHelper has been removed in favor of ProgressBar.';
    }
}
