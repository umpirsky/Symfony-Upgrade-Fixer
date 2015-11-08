<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormTypeNamesFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            $types = [
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

            foreach ($types as $type) {
                if (null === $this->matchTypeName($tokens, $type)) {
                    continue;
                }

                $this->addTypeUse($tokens, $type);
                $this->fixTypeNames($tokens, $type);
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Instead of referencing types by name, you should reference them by their fully-qualified class name (FQCN) instead.';
    }

    private function isFormType(Tokens $tokens)
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

    private function addTypeUse(Tokens $tokens, $name)
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

    private function fixTypeNames(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchTypeName($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 1];

        $tokens->insertAt(
            $matchedIndex,
            [
                new Token([T_STRING, $name.'Type']),
                new Token([T_DOUBLE_COLON, '::']),
            ]
        );
        $matchedTokens[$matchedIndex]->override([10002, 'class']);

        $this->fixTypeNames($tokens, $name);
    }

    private function matchTypeName(Tokens $tokens, $name)
    {
        return $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, 'add'],
            '(',
            [T_CONSTANT_ENCAPSED_STRING],
            ',',
            [T_CONSTANT_ENCAPSED_STRING, sprintf("'%s'", strtolower($name))],
        ]);
    }
}
