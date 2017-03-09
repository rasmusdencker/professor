<?php namespace Selveo\Professor;

use Selveo\Professor\Evaluator\EvaluatorFunction;
use Selveo\Professor\Exceptions\EvaluatorException;
use Selveo\Professor\Lexer\Token;
use Selveo\Professor\Lexer\TokenizedExpression;
use Selveo\Professor\Lexer\Tokenizer;
use Selveo\Professor\Utils\Variable;
use Selveo\Professor\Utils\VariableExtractor;

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
     * @return float|float[]
     * @throws EvaluatorException
     */
    protected function evaluateToken(Token $token, $result, $iterator)
    {
        switch ($token->getType()) {
            case Tokenizer::TYPE_NUMBER:
                return floatval( $token->getValue() );
                break;

            case Tokenizer::TYPE_UNWINDER:
                return $this->unwind($token->getSubExpression());
                break;

            case Tokenizer::TYPE_FUNCTION:
                $functionName = strtolower($token->getValue());

                if(!isset($this->functions[$functionName])) {
                    throw new EvaluatorException("Unknown function '{$token->getValue()}'.");
                }

                $iterator->next();

                return $this->functions[$functionName]->evaluate(
                    $this->collectFunctionArguments($iterator)
                );

            case Tokenizer::TYPE_OPEN_PARENTHESIS:
                return $this->evaluateUntilMatchingCloseParenthesis($iterator);

            case Tokenizer::TYPE_VARIABLE:
            case Tokenizer::TYPE_UNWINDING_VARIABLE:
                return VariableExtractor::extract($token->getValue(), $this->variables);
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
                        return Variable::add($result, $next);

                    case "-":
                        return Variable::subtract($result, $next);

                    case "/":
                        return Variable::divide($result, $next);

                    case "*":
                        return Variable::multiply($result,$next);

                    case "^":
                        return Variable::pow($result, $next);

                    default:
                        return $result;
                }

            default:
                return $result;
        }
    }

    protected function evaluateUntilMatchingCloseParenthesis($iterator)
    {
        $result = 0;
        $opens = 0;

        while($iterator->valid())
        {
            $token = $iterator->current();

            if($token->getType() === Tokenizer::TYPE_CLOSING_PARENTHESIS){
                $opens--;
                if($opens <= 0) break;
            }

            if($token->getType() === Tokenizer::TYPE_OPEN_PARENTHESIS){
                $opens++;
                $iterator->next();
                continue;
            }

            $result = $this->evaluateToken($token, $result, $iterator);
            $iterator->next();
        }

        return $result;
    }

    /**
     * @param $result
     * @param $iterator
     *
     * @return float|\float[]
     */
    protected function next($result, $iterator)
    {
        $iterator->next();

        if (!$iterator->valid()) {
            return $result;
        }

        return $this->evaluateToken($iterator->current(), $result, $iterator);
    }

    private function collectFunctionArguments($iterator)
    {
        $arguments = [];

        $opens = 0;
        $result = 0;
        $merge = false;

        while($iterator->valid())
        {
            $token = $iterator->current();

            if($token->getType() === Tokenizer::TYPE_CLOSING_PARENTHESIS){
                $opens--;
                if($opens <= 0) break;
            }

            if($token->getType() === Tokenizer::TYPE_OPEN_PARENTHESIS){
                $opens++;
                $iterator->next();
                continue;
            }

            // =========================================================
            // Every time we meet an argument separator, append the last
            // result to the argument array and go to the next token.
            // =========================================================
            if($token->getType() === Tokenizer::TYPE_ARGUMENT_SEPARATOR)
            {
                if( $merge ) {
                    $arguments += $result;
                }
                else {
                    $arguments[] = $result;
                }
                $result = 0;
                $iterator->next();
                continue;
            }

            $result = $this->evaluateToken($token, $result, $iterator);
            $merge  = $token->getType() === Tokenizer::TYPE_UNWINDER;
            $iterator->next();
        }

        // =========================================
        // Add the last result to the argument array
        // =========================================
        if( $merge ) {
            $arguments += $result;
        }
        else {
            $arguments[] = $result;
        }

        return $arguments;

    }

    private function unwind(TokenizedExpression $rawExpression)
    {
        return $this->evaluate($rawExpression, $this->variables);
    }
}