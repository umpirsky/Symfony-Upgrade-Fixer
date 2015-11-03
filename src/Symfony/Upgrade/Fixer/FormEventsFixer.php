<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class FormEventsFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->hasUseDeclarations(
            $tokens,
            ['Symfony', 'Component', 'Form', 'FormEvents']
        );

        if ($used) {
            $this->fixUsages($tokens);
        }

        return $tokens->generateCode();
    }

    private function fixUsages(Tokens $tokens)
    {
        $this->fixPrefixUsages($tokens, 'PRE_');
        $this->fixPrefixUsages($tokens);
        $this->fixPrefixUsages($tokens, 'POST_');
    }

    private function fixPrefixUsages(Tokens $tokens, $prefix = '')
    {
        $formEventTokens = $tokens->findSequence([
            [T_STRING, 'FormEvents'],
            [T_DOUBLE_COLON],
            [T_STRING, $prefix.'BIND']
       ]);

        if (null === $formEventTokens) {
            return;
        }

        $formEventTokensIndexes = array_keys($formEventTokens);

        $formEventTokens[$formEventTokensIndexes[count($formEventTokensIndexes) - 1]]
            ->setContent($prefix.'SUBMIT')
        ;

        $this->fixUsages($tokens);
    }

    public function getDescription()
    {
        return 'Renamed FormEvents::*_BIND to FormEvents::*_SUBMIT.';
    }
}
