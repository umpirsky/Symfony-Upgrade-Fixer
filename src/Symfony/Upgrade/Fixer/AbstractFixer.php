<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\AbstractFixer as BaseAbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class AbstractFixer extends BaseAbstractFixer
{
    protected function hasUseStatements(Tokens $tokens, array $fqcn)
    {
        return null !== $this->getUseStatements($tokens, $fqcn);
    }

    protected function getUseStatements(Tokens $tokens, array $fqcn)
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

    protected function renameUseStatements(Tokens $tokens, array $oldFqcn, $newClassName)
    {
        $matchedTokens = $this->getUseStatements($tokens, $oldFqcn);
        if (null === $matchedTokens) {
            return false;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $classNameToken = $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 2]];
        $classNameToken->setContent($newClassName);

        return true;
    }

    protected function addUseStatement(Tokens $tokens, array $fqcn, $alias = null)
    {
        if ($this->hasUseStatements($tokens, $fqcn)) {
            return;
        }

        $importUseIndexes = $tokens->getImportUseIndexes();
        if (!isset($importUseIndexes[0])) {
            return;
        }

        $fqcnTokens = [];
        foreach ($fqcn as $fqcnComponent) {
            $fqcnTokens[] = new Token([T_STRING, $fqcnComponent]);
            $fqcnTokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }
        array_pop($fqcnTokens);

        if ($alias) {
            $fqcnTokens[] = new Token([T_WHITESPACE, ' ']);
            $fqcnTokens[] = new Token([T_AS, 'as']);
            $fqcnTokens[] = new Token([T_WHITESPACE, ' ']);
            $fqcnTokens[] = new Token([T_STRING, $alias]);
        }

        $tokens->insertAt(
            $importUseIndexes[0],
            array_merge(
                [
                    new Token([T_USE, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                ],
                $fqcnTokens,
                [
                    new Token(';'),
                    new Token([T_WHITESPACE, PHP_EOL]),
                ]
            )
        );
    }

    protected function extendsClass(Tokens $tokens, array $fqcn)
    {
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

        return $tokens->findSequence([
            [T_CLASS],
            [T_STRING],
            [T_EXTENDS],
            [T_STRING, array_pop($fqcn)],
        ]);
    }
}
