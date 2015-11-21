<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class GetRequestFixer extends AbstractFixer
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isController($tokens)) {
            $this->fixGetRequests($tokens);
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'The getRequest method of the base controller class was removed, request object is injected in the action method instead.';
    }

    private function isController(Tokens $tokens)
    {
        return $this->extendsClass($tokens, ['Symfony', 'Bundle', 'FrameworkBundle', 'Controller', 'Controller']);
    }

    private function fixGetRequests(Tokens $tokens)
    {
        $todo = [];

        foreach ($this->getClassyElementsIndexed($tokens) as $i => $element) {
            if ('method' === $element['type']) {
                if ($this->fixActionParameters($tokens, $element['index'])) {
                    $todo[] = $i;
                }
            }
        }

        foreach ($this->getClassyElementsIndexed($tokens) as $i => $element) {
            if (in_array($i, $todo)) {
                $this->fixActionBody($tokens, $element['index']);
            }
        }
    }

    private function getClassyElementsIndexed(Tokens $tokens)
    {
        $elements = [];

        foreach ($tokens->getClassyElements() as $index => $element) {
            $element['index'] = $index;
            $elements[] = $element;
        }

        return $elements;
    }

    private function fixActionParameters(Tokens $tokens, $index)
    {
        if (!$this->isAction($tokens, $index)) {
            return;
        }

        $parenthesisIndexes = $this->getActionParenthesisIndexes($tokens, $index);
        if ($this->hasRequestParameterSequence($tokens, $parenthesisIndexes[0], $parenthesisIndexes[1])) {
            return;
        }

        $curlyBraceIndexes = $this->getActionCurlyBraceIndexes($tokens, $index);
        if (!$this->hasGetRequestSequence($tokens, $curlyBraceIndexes[0], $curlyBraceIndexes[1])) {
            return;
        }

        if (null === $requestVariableName = $this->getRequestVariableName($tokens, $curlyBraceIndexes[0], $curlyBraceIndexes[1])) {
            return;
        }

        $insertAt = $tokens->getNextMeaningfulToken($parenthesisIndexes[0]);

        $tokens->insertAt(
            $insertAt,
            array_merge(
                [
                    new Token([T_STRING, 'Request']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_VARIABLE, $requestVariableName]),
                ],
                $parenthesisIndexes[1] - $parenthesisIndexes[0] > 1 ? [new Token(','), new Token([T_WHITESPACE, ' '])] : []
            )
        );

        $this->addUseStatement(
            $tokens,
            ['Symfony', 'Component', 'HttpFoundation', 'Request']
        );

        return true;
    }

    private function fixActionBody(Tokens $tokens, $index)
    {
        $curlyBraceIndexes = $this->getActionCurlyBraceIndexes($tokens, $index);
        if (!$this->hasGetRequestSequence($tokens, $curlyBraceIndexes[0], $curlyBraceIndexes[1])) {
            return;
        }

        $clearFrom = $tmp = $this->getRequestVariableTokenIndex($tokens, $curlyBraceIndexes[0], $curlyBraceIndexes[1]);
        $keys = array_keys($this->getGetRequestSequence($tokens, $curlyBraceIndexes[0], $curlyBraceIndexes[1]));
        $clearTo = array_pop($keys);
        $clearTo = $tokens->getNextMeaningfulToken($clearTo);
        if (!$tokens[$clearTo]->equals(';')) {
            return;
        }

        $tokens->clearRange($clearFrom, $clearTo);
        $tokens->removeTrailingWhitespace($clearTo);
    }

    private function isAction(Tokens $tokens, $index)
    {
        $actionNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if (!$actionNameToken->isGivenKind(T_STRING)) {
            return false;
        }
        if (substr($actionNameToken->getContent(), -6) !== 'Action') {
            return false;
        }

        return true;
    }

    private function getActionParenthesisIndexes(Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextMeaningfulToken($tokens->getNextMeaningfulToken($index));
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

        return [$openIndex, $closeIndex];
    }

    private function getActionCurlyBraceIndexes(Tokens $tokens, $index)
    {
        $openIndex = $tokens->getNextMeaningfulToken(
            $this->getActionParenthesisIndexes($tokens, $index)[1]
        );
        $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);

        return [$openIndex, $closeIndex];
    }

    private function hasRequestParameterSequence(Tokens $tokens, $start, $end)
    {
        return null !== $tokens->findSequence([
            [T_STRING, 'Request'],
            [T_VARIABLE],
        ], $start, $end);
    }

    private function hasGetRequestSequence(Tokens $tokens, $start, $end)
    {
        return null !== $this->getGetRequestSequence($tokens, $start, $end);
    }

    private function getGetRequestSequence(Tokens $tokens, $start, $end)
    {
        return $tokens->findSequence([
            [T_VARIABLE, '$this'],
            [T_OBJECT_OPERATOR],
            [T_STRING, 'getRequest'],
            '(',
            ')',
        ], $start, $end);
    }

    private function getRequestVariableName(Tokens $tokens, $start, $end)
    {
        $requestVariableTokenIndex = $this->getRequestVariableTokenIndex($tokens, $start, $end);
        $requestVariableToken = $tokens[$requestVariableTokenIndex];
        if (!$requestVariableToken->isGivenKind(T_VARIABLE)) {
            return;
        }

        return $requestVariableToken->getContent();
    }

    private function getRequestSequenceStartTokenIndex(Tokens $tokens, $start, $end)
    {
        $getRequestSequence = $this->getGetRequestSequence($tokens, $start, $end);

        return array_keys($getRequestSequence)[0];
    }

    private function getRequestVariableTokenIndex(Tokens $tokens, $start, $end)
    {
        $getRequestSequenceStartIndex = $this->getRequestSequenceStartTokenIndex($tokens, $start, $end);

        $assignTokenIndex = $tokens->getPrevMeaningfulToken($getRequestSequenceStartIndex);
        if ('=' !== $tokens[$assignTokenIndex]->getContent()) {
            return;
        }

        return $tokens->getPrevMeaningfulToken($assignTokenIndex);
    }
}
