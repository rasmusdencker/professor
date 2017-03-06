<?php namespace Professor;

use Professor\Evaluator\EvaluatorFunction;
use Professor\Exceptions\EvaluatorException;
use Professor\Lexer\Token;
use Professor\Lexer\TokenizedExpression;
use Professor\Lexer\Tokenizer;
use Professor\Utils\VariableExtractor;

class Evaluator
{
    protected $functions = [];
    protected $variables = [];

    public function evaluate(TokenizedExpression $expression, array $variables = [])
    {
        $result = 0;
        $this->variables = $variables;

        $iterator = $expression->getIterator();

        while($iterator->valid())
        {
            /** @var Token $token */
            $token = $iterator->current();

            $result = $this->evaluateToken($token, $result, $iterator);

            $iterator->next();
        }

        return $result;
    }

    public function addFunction(string $functionName, callable $callback, int $minArgs = 0, $maxArgs = null)
    {
        $this->functions[strtolower($functionName)] = new EvaluatorFunction($callback,$minArgs,$maxArgs);

        return $this;
    }

    /**
     * @param Token $token
     * @param       $result
     * @param       $iterator
     *
     * @param array $variables
     *
     * @return float
     * @throws EvaluatorException
     */
    protected function evaluateToken(Token $token, $result, $iterator) : float
    {
        switch ($token->getType()) {
            case Tokenizer::TYPE_NUMBER:
                return floatval( $token->getValue() );
                break;

            case Tokenizer::TYPE_FUNCTION:
                $functionName = strtolower($token->getValue());

                if(!isset($this->functions[$functionName])) throw new EvaluatorException("Unknown function '{$token->getValue()}'.");

                $iterator->next();
                $iterator->next();

                return $this->functions[$functionName]->evaluate(
                    $this->collectFunctionArguments($iterator)
                );

            case Tokenizer::TYPE_OPEN_PARENTHESIS:
                return $this->evaluateUntilMatchingCloseParenthesis($iterator);
                break;

            case Tokenizer::TYPE_VARIABLE:
                return VariableExtractor::extract($token->getValue(), $this->variables);
                break;

            case Tokenizer::TYPE_CLOSING_PARENTHESIS:
                $iterator->next(); // Skip the paranthesis

                if(!$iterator->valid())
                {
                    return $result;
                }

                return $this->evaluateToken($iterator->current(), $result, $iterator);
                break;

            case Tokenizer::TYPE_OPERATOR:
                $iterator->next();

                if(!$iterator->valid())
                {
                    return $result;
                }

                $next = $this->evaluateToken($iterator->current(),$result,$iterator);

                switch ($token->getValue()) {
                    case "+":
                        return $result + $next;
                        break;

                    case "-":
                        return $result - $next;
                        break;

                    case "/":
                        return $result / $next;
                        break;

                    case "*":
                        return $result * $next;
                        break;

                    case "^":
                        return pow($result, $next);
                        break;
                }
                break;

            default:
                break;
        }

        return $result;
    }

    protected function evaluateUntilMatchingCloseParenthesis($iterator)
    {
        $iterator->next();
        $result = 0;

        while($iterator->valid() && ($token = $iterator->current())->getType() !== Tokenizer::TYPE_CLOSING_PARENTHESIS)
        {
            $result = $this->evaluateToken($token, $result, $iterator);
            $iterator->next();
        }

        return $result;
    }

    private function collectFunctionArguments($iterator)
    {
        $arguments = [];

        $ignore = [
            Tokenizer::TYPE_ARGUMENT_SEPARATOR,
            Tokenizer::TYPE_CLOSING_PARENTHESIS
        ];

        while($iterator->valid())
        {
            $token = $iterator->current();

            if(!in_array($token->getType(), $ignore))
            {
                $arguments[] = $this->evaluateToken($token, 0, $iterator);
            }


            $iterator->next();
        }

        return $arguments;

    }
}