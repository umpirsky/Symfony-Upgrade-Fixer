<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

abstract class RenameFixer extends AbstractFixer
{
    protected function renameUseStatements(Tokens $tokens, array $oldFqcn, $newClassName)
    {
        $matchedTokens = $this->getUseDeclarations($tokens, $oldFqcn);
        if (null === $matchedTokens) {
            return false;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $classNameToken = $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 2]];
        $classNameToken->setContent($newClassName);

        return true;
    }

    protected function renameNewStatements(Tokens $tokens, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_NEW],
            [T_STRING, $old],
       ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedndexes[count($matchedndexes) - 1]]
            ->setContent($new)
        ;

        $this->renameNewStatements($tokens, $old, $new);
    }

    protected function renameMethodCalls(Tokens $tokens, $className, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_STRING, $className],
            [T_DOUBLE_COLON],
            [T_STRING, $old],
            '(',
            ')',
       ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 3]]
            ->setContent($new)
        ;

        $this->renameMethodCalls($tokens, $className, $old, $new);
    }

    protected function renameConstants(Tokens $tokens, $className, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_STRING, $className],
            [T_DOUBLE_COLON],
            [T_STRING, $old]
       ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 1]]
            ->setContent($new)
        ;

        $this->renameConstants($tokens, $className, $old, $new);
    }
}
