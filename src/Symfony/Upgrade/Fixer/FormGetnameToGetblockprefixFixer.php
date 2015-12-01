<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormGetnameToGetblockprefixFixer extends FormTypeFixer
{

    /**
     * Fixes a file.
     *
     * @param \SplFileInfo $file A \SplFileInfo instance
     * @param string       $content The file content
     *
     * @return string The fixed file content
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens) && null !== $this->matchGetNameMethod($tokens)) {
            $this->fixGetNameMethod($tokens);
        }

        return $tokens->generateCode();
    }

    private function matchGetNameMethod(Tokens $tokens)
    {
        return $tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'getName'],
            '(',
            ')'
        ]);
    }

    private function fixGetNameMethod(Tokens $tokens)
    {
        $matchedTokens = $this->matchGetNameMethod($tokens);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);
        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 3];
        $matchedTokens[$matchedIndex]->setContent('getBlockPrefix');
    }

    /**
     * Returns the description of the fixer.
     *
     * A short one-line description of what the fixer does.
     *
     * @return string The description of the fixer
     */
    public function getDescription()
    {
        return 'The method FormTypeInterface::getName() was deprecated and will be removed in Symfony 3.0. You should now implement FormTypeInterface::getBlockPrefix() instead.';
    }
}
