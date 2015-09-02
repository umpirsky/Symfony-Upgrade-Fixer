<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class PropertyAccessFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->hasUseDeclarations(
            $tokens,
            ['Symfony', 'Component', 'PropertyAccess', 'PropertyAccess']
        );

        if ($used) {
            $this->fixUsages($tokens);
        }

        return $tokens->generateCode();
    }

    private function fixUsages(Tokens $tokens)
    {
        $propertyAccessTokens = $tokens->findSequence([
            [T_STRING, 'PropertyAccess'],
            [T_DOUBLE_COLON],
            [T_STRING, 'getPropertyAccessor'],
            '(',
            ')',
       ]);

        if (null === $propertyAccessTokens) {
            return;
        }

        $propertyAccessTokensIndexes = array_keys($propertyAccessTokens);

        $propertyAccessTokens[$propertyAccessTokensIndexes[count($propertyAccessTokensIndexes) - 3]]
            ->setContent('createPropertyAccessor')
        ;

        $this->fixUsages($tokens);
    }

    public function getDescription()
    {
        return 'Renamed PropertyAccess::getPropertyAccessor to PropertyAccess::createPropertyAccessor.';
    }
}
