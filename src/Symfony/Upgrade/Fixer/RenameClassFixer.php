<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

abstract class RenameClassFixer extends RenameFixer
{
    protected function rename($content, array $oldFqcn, $newClassName)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->renameUseStatements(
            $tokens,
            $oldFqcn,
            $newClassName
        );

        if ($used) {
            $this->renameNewStatements($tokens, array_pop($oldFqcn), $newClassName);
        }

        return $tokens->generateCode();
    }
}
