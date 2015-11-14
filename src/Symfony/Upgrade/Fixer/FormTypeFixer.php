<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class FormTypeFixer extends AbstractFixer
{
    protected $types = [
        'Birthday',
        'Button',
        'Checkbox',
        'Choice',
        'Collection',
        'Country',
        'Currency',
        'DateTime',
        'Email',
        'File',
        'Hidden',
        'Integer',
        'Language',
        'Locale',
        'Money',
        'Number',
        'Password',
        'Percent',
        'Radio',
        'Range',
        'Repeated',
        'Reset',
        'Search',
        'Submit',
        'Text',
        'Textarea',
        'Time',
        'Timezone',
        'Url',
    ];

    protected function isFormType(Tokens $tokens)
    {
        if (!$this->hasUseStatements($tokens, ['Symfony', 'Component', 'Form', 'AbstractType'])) {
            return false;
        }

        return null !== $tokens->findSequence([
            [T_CLASS],
            [T_STRING],
            [T_EXTENDS],
            [T_STRING, 'AbstractType'],
        ]);
    }

    protected function addTypeUse(Tokens $tokens, $name)
    {
        $this->addUseStatement(
            $tokens,
            ['Symfony', 'Component', 'Form', 'Extension', 'Core', 'Type', ucfirst($name).'Type']
        );
    }
}
