<?php namespace Selveo\Professor;

use Selveo\Professor\Lexer\FunctionTokenizer;
use Selveo\Professor\Lexer\Token;
use Selveo\Professor\Lexer\TokenizedExpression;
use Selveo\Professor\Lexer\Tokenizer;
use Selveo\Professor\Lexer\UnwinderToken;
use Selveo\Professor\Utils\StringCoverage;

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

                    if ($tokenizer->type() === Tokenizer::TYPE_UNWINDER) {
                        $tokens[$position] = new UnwinderToken(
                            $this->tokenize($match)
                        );
                        continue;
                    }

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
        $this->addTokenizer('\[\.{3}(.+?)\]', Tokenizer::TYPE_UNWINDER);
        $this->addTokenizer('-?\d+(?:\.\d+)?', Tokenizer::TYPE_NUMBER);
        $this->addTokenizer('@([a-zA-Z_]+\.[a-zA-Z_.]*\*(?:$|[a-z_A-Z.*]+))', Tokenizer::TYPE_UNWINDING_VARIABLE);
        $this->addTokenizer('[+\-*\/]', Tokenizer::TYPE_OPERATOR);
        $this->addTokenizer('\(', Tokenizer::TYPE_OPEN_PARENTHESIS);
        $this->addTokenizer('\)', Tokenizer::TYPE_CLOSING_PARENTHESIS);
        $this->addTokenizer(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR);
        $this->addTokenizer('@([a-zA-Z_.]+)', Tokenizer::TYPE_VARIABLE);
    }

}