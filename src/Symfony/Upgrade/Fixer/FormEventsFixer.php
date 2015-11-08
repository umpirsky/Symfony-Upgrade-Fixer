<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class FormEventsFixer extends RenameFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $used = $this->hasUseDeclarations(
            $tokens,
            ['Symfony', 'Component', 'Form', 'FormEvents']
        );

        if ($used) {
            $this->renameConstants($tokens, 'FormEvents', 'PRE_BIND', 'PRE_SUBMIT');
            $this->renameConstants($tokens, 'FormEvents', 'BIND', 'SUBMIT');
            $this->renameConstants($tokens, 'FormEvents', 'POST_BIND', 'POST_SUBMIT');
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'The events PRE_BIND, BIND and POST_BIND were renamed to PRE_SUBMIT, SUBMIT and POST_SUBMIT.';
    }
}
