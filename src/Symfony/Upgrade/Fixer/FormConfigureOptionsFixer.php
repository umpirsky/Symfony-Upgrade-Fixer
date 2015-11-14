<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormConfigureOptionsFixer extends FormTypeFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens) && null !== $this->matchSetDefaultOptionsMethod($tokens)) {
            $this->fixConfigureOptions($tokens);
            $this->fixUseStatements($tokens);
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'The method AbstractType::setDefaultOptions(OptionsResolverInterface $resolver) have been renamed to AbstractType::configureOptions(OptionsResolver $resolver).';
    }

    private function matchSetDefaultOptionsMethod(Tokens $tokens)
    {
        return $tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'setDefaultOptions'],
            '(',
        ]);
    }

    private function fixConfigureOptions(Tokens $tokens)
    {
        $matchedTokens = $this->matchSetDefaultOptionsMethod($tokens);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);
        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 2];
        $matchedTokens[$matchedIndex]->override([T_STRING, 'configureOptions']);

        $typeHint = $tokens[$tokens->getNextMeaningfulToken(end($matchedIndexes))];

        if (!$typeHint->isGivenKind(T_STRING) || $typeHint->getContent() !== 'OptionsResolverInterface') {
            return;
        }

        $typeHint->setContent('OptionsResolver');
    }

    private function fixUseStatements(Tokens $tokens)
    {
        $classIndexes = array_keys($tokens->findGivenKind(T_CLASS));

        if ($tokens->findSequence([[T_STRING, 'OptionsResolverInterface']], array_pop($classIndexes))) {
            return $this->addUseStatement($tokens, ['Symfony', 'Component', 'OptionsResolver', 'OptionsResolver']);
        }

        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'OptionsResolver', 'OptionsResolverInterface'], 'OptionsResolver');
    }
}
