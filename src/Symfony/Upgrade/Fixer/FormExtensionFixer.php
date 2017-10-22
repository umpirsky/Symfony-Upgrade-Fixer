<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

abstract class FormExtensionFixer extends FormTypeFixer
{
    protected function isFormType(Tokens $tokens)
    {
        return $this->extendsClass($tokens, ['Symfony', 'Component', 'Form', 'AbstractTypeExtension']);
    }
}
