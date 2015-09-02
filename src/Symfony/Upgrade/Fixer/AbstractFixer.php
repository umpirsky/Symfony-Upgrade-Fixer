<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\AbstractFixer as BaseAbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

abstract class AbstractFixer extends BaseAbstractFixer
{
    protected function hasUseDeclarations(Tokens $tokens, array $fqcn)
    {
        $useTokens = $this->getUseDeclarations($tokens, $fqcn);

        return null !== $useTokens;
    }

    protected function fixUseDeclarations(Tokens $tokens, array $fqcn, $className)
    {
        $useTokens = $this->getUseDeclarations($tokens, $fqcn);
        if (null === $useTokens) {
            return false;
        }

        $useTokensIndexes = array_keys($useTokens);

        $classNameToken = $useTokens[$useTokensIndexes[count($useTokensIndexes) - 2]];
        $classNameToken->setContent($className);

        return true;
    }

    private function getUseDeclarations(Tokens $tokens, array $fqcn)
    {
        $sequence = [[T_USE]];

        foreach ($fqcn as $component) {
            $sequence = array_merge(
                $sequence,
                [[T_STRING, $component], [T_NS_SEPARATOR]]
            );
        }

        $sequence[count($sequence) - 1] = ';';

        return $tokens->findSequence($sequence);
    }
}
