<?php

use Professor\Lexer\Token;
use Professor\Lexer\TokenizedExpression;
use Professor\Lexer\Tokenizer;

class EvaluatorVariableTest extends EvaluatorTestCase
{
    /** @test **/
    function it_calculates_with_variables()
    {
        $expression = new TokenizedExpression([
            new Token('foo', Tokenizer::TYPE_VARIABLE)
        ]);

        $this->assertEquals(
            50,
            $this->evaluator->evaluate($expression, ["foo" => "50"])
        );

        $expression = new TokenizedExpression([
            new Token('foo', Tokenizer::TYPE_VARIABLE),
            new Token('+', Tokenizer::TYPE_OPERATOR),
            new Token('bar', Tokenizer::TYPE_VARIABLE)
        ]);

        $this->assertEquals(
            75,
            $this->evaluator->evaluate($expression, ["foo" => "50", "bar" => "25"])
        );

        $expression = new TokenizedExpression([
            new Token('foo', Tokenizer::TYPE_VARIABLE),
            new Token('+', Tokenizer::TYPE_OPERATOR),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('bar', Tokenizer::TYPE_VARIABLE),
            new Token('*', Tokenizer::TYPE_OPERATOR),
            new Token('baz', Tokenizer::TYPE_VARIABLE),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS)
        ]);

        $this->assertEquals(
            60,
            $this->evaluator->evaluate($expression, ["foo" => "50", "bar" => "5", "baz" => "2"])
        );

        $expression = new TokenizedExpression([
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('foo', Tokenizer::TYPE_VARIABLE),
            new Token('+', Tokenizer::TYPE_OPERATOR),
            new Token('bar', Tokenizer::TYPE_VARIABLE),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token('*', Tokenizer::TYPE_OPERATOR),
            new Token('baz', Tokenizer::TYPE_VARIABLE),
        ]);

        $this->assertEquals(
            110,
            $this->evaluator->evaluate($expression, ["foo" => "50", "bar" => "5", "baz" => "2"])
        );
    }

    /** @test **/
    function it_calculates_with_nested_variables()
    {
        $variables  = [
            "foo" => [
                "bar" => "5",
                "baz" => "2"
            ]
        ];

        $expression = new TokenizedExpression([
            new Token('foo.bar', Tokenizer::TYPE_VARIABLE),
        ]);

        $this->assertEquals(
            5,
            $this->evaluator->evaluate($expression, $variables)
        );

        $expression = new TokenizedExpression([
            new Token('foo.bar', Tokenizer::TYPE_VARIABLE),
            new Token('+', Tokenizer::TYPE_OPERATOR),
            new Token('foo.baz', Tokenizer::TYPE_VARIABLE),
        ]);

        $this->assertEquals(
            7,
            $this->evaluator->evaluate($expression, $variables)
        );
    }

}