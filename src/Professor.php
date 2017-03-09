<?php namespace Selveo\Professor;

class Professor
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var Evaluator
     */
    private $evaluator;

    /**
     * Professor constructor.
     *
     * @param Lexer     $lexer
     * @param Evaluator $evaluator
     */
    public function __construct(Lexer $lexer, Evaluator $evaluator)
    {
        $this->lexer     = $lexer;
        $this->evaluator = $evaluator;
    }

    /**
     * @return Lexer
     * @codeCoverageIgnore
     */
    public function getLexer(): Lexer
    {
        return $this->lexer;
    }

    /**
     * @return Evaluator
     * @codeCoverageIgnore
     */
    public function getEvaluator(): Evaluator
    {
        return $this->evaluator;
    }

    /**
     * Evaluates an expression.
     *
     * @param string $expression
     * @param array  $variables
     *
     * @return float|int
     */
    public function calculate(string $expression, array $variables = [])
    {
        return $this->evaluator->evaluate(
            $this->lexer->tokenize($expression),
            $variables
        );
    }

    /**
     * Adds a function to this professors lexer and evaluator.
     *
     * @param $functionName
     * @param $callback
     */
    public function addFunction($functionName, $callback)
    {
        $this->evaluator->addFunction($functionName, $callback);
        $this->lexer->addTokenizer($functionName, Lexer\Tokenizer::TYPE_FUNCTION);
    }
}