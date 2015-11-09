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
        if (!$this->hasUseDeclarations($tokens, ['Symfony', 'Component', 'Form', 'AbstractType'])) {
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
        if ($this->hasUseDeclarations($tokens, ['Symfony', 'Component', 'Form', 'Extension', 'Core', 'Type', ucfirst($name).'Type'])) {
            return;
        }

        $importUseIndexes = $tokens->getImportUseIndexes();
        if (!isset($importUseIndexes[0])) {
            return;
        }

        $tokens->insertAt(
            $importUseIndexes[0],
            [
                new Token([T_USE, 'use']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'Symfony']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Component']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Form']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Extension']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Core']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, 'Type']),
                new Token([T_NS_SEPARATOR, '\\']),
                new Token([T_STRING, ucfirst($name).'Type']),
                new Token(';'),
                new Token([T_WHITESPACE, PHP_EOL]),
            ]
        );
    }
}
