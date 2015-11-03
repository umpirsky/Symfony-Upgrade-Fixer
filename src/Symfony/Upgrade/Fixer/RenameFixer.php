<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

abstract class RenameFixer extends AbstractFixer
{
    protected function renameUseDeclarations(Tokens $tokens, array $oldFqcn, $newClassName)
    {
        $useTokens = $this->getUseDeclarations($tokens, $oldFqcn);
        if (null === $useTokens) {
            return false;
        }

        $useTokensIndexes = array_keys($useTokens);

        $classNameToken = $useTokens[$useTokensIndexes[count($useTokensIndexes) - 2]];
        $classNameToken->setContent($newClassName);

        return true;
    }

    protected function renameUsages(Tokens $tokens, $old, $new)
    {
        $newTokens = $tokens->findSequence([
            [T_NEW],
            [T_STRING, $old],
       ]);

        if (null === $newTokens) {
            return;
        }

        $newTokensIndexes = array_keys($newTokens);

        $newTokens[$newTokensIndexes[count($newTokensIndexes) - 1]]
            ->setContent($new)
        ;

        $this->renameUsages($tokens, $old, $new);
    }
}
