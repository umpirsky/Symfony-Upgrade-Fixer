<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class FormGetnameToGetblockprefixFixer extends FormTypeFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            $this->fixGetNameMethod($tokens);
        }

        return $tokens->generateCode();
    }

    private function hasBlockPrefix(Tokens $tokens)
    {
        return count($tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'getBlockPrefix'],
            '(',
            ')',
        ])) > 0;
    }

    private function matchGetNameMethod(Tokens $tokens)
    {
        return $tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'getName'],
            '(',
            ')',
        ]);
    }

    private function fixGetNameMethod(Tokens $tokens)
    {
        $matchedTokens = $this->matchGetNameMethod($tokens);
        if (null === $matchedTokens || $this->hasBlockPrefix($tokens)) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);
        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 3];
        $matchedTokens[$matchedIndex]->setContent('getBlockPrefix');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The method FormTypeInterface::getName() was deprecated, you should now implement FormTypeInterface::getBlockPrefix() instead.';
    }
}
