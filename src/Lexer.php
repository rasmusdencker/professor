<?php namespace Professor;

use Professor\Lexer\FunctionTokenizer;
use Professor\Lexer\Token;
use Professor\Lexer\TokenizedExpression;
use Professor\Lexer\Tokenizer;
use Professor\Utils\StringCoverage;

/**
 * Class Lexer
 *
 * @package Professor
 */
class Lexer
{
    /**
     * @var array
     */
    protected $tokenizers = [];

    /**
     * Lexer constructor.
     */
    public function __construct()
    {
        $this->addBaseTokenizers();
    }

    /**
     * Adds a Tokenizer.
     *
     * @param string|Tokenizer $pattern
     * @param string|null      $type
     *
     * @return $this
     */
    public function addTokenizer($pattern, string $type = null)
    {
        if (!$pattern instanceof Tokenizer) {
            $pattern = new Tokenizer($pattern, $type);
        }

        $this->tokenizers[] = $pattern;

        return $this;
    }

    /**
     * Tokenizes a mathematical expression
     *
     * @param string $expression
     *
     * @return TokenizedExpression
     */
    public function tokenize(string $expression): TokenizedExpression
    {
        $tokens   = [];
        $coverage = new StringCoverage($expression);

        /** @var Tokenizer $tokenizer */
        foreach ($this->tokenizers as $tokenizer) {
            $matchGroups = [];

            if (!preg_match_all($tokenizer->matcher(), $expression, $matchGroups, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            // =========================================================
            // Remove the full match to access only the captured results
            // =========================================================
            array_shift($matchGroups);

            foreach ($matchGroups as $matches) {
                foreach ($matches as $match) {
                    list($match, $position) = $match;

                    if ($coverage->isPartlyCovered($position, strlen($match))) {
                        continue;
                    }

                    $coverage->markCovered($position, strlen($match));

                    $tokens[$position] = new Token($match, $tokenizer->type());
                }
            }
        }

        ksort($tokens);

        return new TokenizedExpression($tokens);
    }

    public function addFunctionTokenizer(string $functionName)
    {
        return $this->addTokenizer($functionName, Tokenizer::TYPE_FUNCTION);
    }

    private function addBaseTokenizers()
    {
        $this->addTokenizer('-?\d+(?:\.\d+)?', Tokenizer::TYPE_NUMBER);
        $this->addTokenizer('[+\-*\/]', Tokenizer::TYPE_OPERATOR);
        $this->addTokenizer('\(', Tokenizer::TYPE_OPEN_PARENTHESIS);
        $this->addTokenizer('\)', Tokenizer::TYPE_CLOSING_PARENTHESIS);
        $this->addTokenizer(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR);
        $this->addTokenizer('@([a-zA-Z.]+)', Tokenizer::TYPE_VARIABLE);
    }

}