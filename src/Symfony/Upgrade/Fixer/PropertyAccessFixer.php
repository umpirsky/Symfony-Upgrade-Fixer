<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class PropertyAccessFixer extends RenameFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->hasUseStatements(
            $tokens,
            ['Symfony', 'Component', 'PropertyAccess', 'PropertyAccess']
        );

        if ($used) {
            $this->renameMethodCalls($tokens, 'PropertyAccess', 'getPropertyAccessor', 'createPropertyAccessor');
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Renamed PropertyAccess::getPropertyAccessor to PropertyAccess::createPropertyAccessor.';
    }
}
