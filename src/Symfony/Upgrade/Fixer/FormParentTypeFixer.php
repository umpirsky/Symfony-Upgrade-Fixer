<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormParentTypeFixer extends FormTypeFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            foreach ($this->types as $type) {
                if (null === $this->matchGetParentMethod($tokens, $type)) {
                    continue;
                }

                $this->addTypeUse($tokens, $type);
                $this->fixParentTypes($tokens, $type);
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Returning type instances from FormTypeInterface::getParent() is deprecated, return the fully-qualified class name of the parent type class instead.';
    }

    private function matchGetParentMethod(Tokens $tokens, $name)
    {
        return $tokens->findSequence([
            [T_PUBLIC, 'public'],
            [T_FUNCTION],
            [T_STRING, 'getParent'],
            '(',
            ')',
            '{',
            [T_RETURN],
            [T_CONSTANT_ENCAPSED_STRING, sprintf("'%s'", strtolower($name))],
            ';',
            '}',
        ]);
    }

    private function fixParentTypes(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchGetParentMethod($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 3];

        $tokens->insertAt(
            $matchedIndex,
            [
                new Token([T_STRING, $name.'Type']),
                new Token([T_DOUBLE_COLON, '::']),
            ]
        );
        $matchedTokens[$matchedIndex]->override([self::T_CLASS, 'class']);

        $this->fixParentTypes($tokens, $name);
    }
}
