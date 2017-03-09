<?php

use Selveo\Professor\Exceptions\EvaluatorException;
use Selveo\Professor\Lexer\Token;
use Selveo\Professor\Lexer\TokenizedExpression;
use Selveo\Professor\Lexer\Tokenizer;

class EvaluatorTest extends EvaluatorTestCase
{

    /** @test **/
    function it_evaluates_simple_arithmetic_expressions()
    {
        $tokenizedExpression = new TokenizedExpression([
            new Token("3", Tokenizer::TYPE_NUMBER),
            new Token("+", Tokenizer::TYPE_OPERATOR),
            new Token("20", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            23,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("-", Tokenizer::TYPE_OPERATOR),
            new Token("3", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            17,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("/", Tokenizer::TYPE_OPERATOR),
            new Token("4", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            5,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("5", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            100,
            $this->evaluator->evaluate($tokenizedExpression)
        );
    }

    /** @test **/
    function it_evaluates_expressions_with_parenthesis()
    {
        $tokenizedExpression = new TokenizedExpression([
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            100,
            $this->evaluator->evaluate($tokenizedExpression)
        );


        $tokenizedExpression = new TokenizedExpression([
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("+", Tokenizer::TYPE_OPERATOR),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("4", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            100,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("+", Tokenizer::TYPE_OPERATOR),
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("4", Tokenizer::TYPE_NUMBER),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            40,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token("20", Tokenizer::TYPE_NUMBER),
            new Token("+", Tokenizer::TYPE_OPERATOR),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("4", Tokenizer::TYPE_NUMBER),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("0", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            0,
            $this->evaluator->evaluate($tokenizedExpression)
        );
    }

    /** @test **/
    function it_evaluates_exponential_expressions()
    {
        $tokenizedExpression = new TokenizedExpression([
            new Token("10", Tokenizer::TYPE_NUMBER),
            new Token("^", Tokenizer::TYPE_OPERATOR),
            new Token("2", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            100,
            $this->evaluator->evaluate($tokenizedExpression)
        );

        $tokenizedExpression = new TokenizedExpression([
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token("+", Tokenizer::TYPE_OPERATOR),
            new Token("5", Tokenizer::TYPE_NUMBER),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token("^", Tokenizer::TYPE_OPERATOR),
            new Token("2", Tokenizer::TYPE_NUMBER),
        ]);

        $this->assertEquals(
            100,
            $this->evaluator->evaluate($tokenizedExpression)
        );
    }

    /** @test **/
    function it_evaluates_functions()
    {
        $this->evaluator->addFunction("SUM", function(...$args){
            return array_sum($args);
        }, 2);

        // SUM(1,10)
        $expression = new TokenizedExpression([
           new Token("SUM", Tokenizer::TYPE_FUNCTION),
           new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
           new Token("1", Tokenizer::TYPE_NUMBER),
           new Token(",", Tokenizer::TYPE_ARGUMENT_SEPARATOR),
           new Token("10", Tokenizer::TYPE_NUMBER),
           new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            11,
            $this->evaluator->evaluate($expression)
        );



        $expression = new TokenizedExpression([
           new Token("SUM", Tokenizer::TYPE_FUNCTION),
           new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
           new Token("3", Tokenizer::TYPE_NUMBER),
           new Token(",", Tokenizer::TYPE_ARGUMENT_SEPARATOR),
           new Token("10", Tokenizer::TYPE_NUMBER),
            new Token(",", Tokenizer::TYPE_ARGUMENT_SEPARATOR),
           new Token("25", Tokenizer::TYPE_NUMBER),
           new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            38,
            $this->evaluator->evaluate($expression)
        );

        $expression = new TokenizedExpression([
           new Token("SUM", Tokenizer::TYPE_FUNCTION),
           new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
           new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
           new Token("3", Tokenizer::TYPE_NUMBER),
           new Token("*", Tokenizer::TYPE_OPERATOR),
           new Token("3", Tokenizer::TYPE_NUMBER),
           new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
           new Token(",", Tokenizer::TYPE_ARGUMENT_SEPARATOR),
           new Token("10", Tokenizer::TYPE_NUMBER),
           new Token(",", Tokenizer::TYPE_ARGUMENT_SEPARATOR),
           new Token("25", Tokenizer::TYPE_NUMBER),
           new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);
        // SUM((3*3), 10, 25)

        $this->assertEquals(
            44,
            $this->evaluator->evaluate($expression)
        );
    }

    /** @test **/
    function it_adds_to_the_meaning_of_life()
    {
        $this->evaluator->addFunction('MEANINGOFLIFE', function($base){
            return $base + 42;
        });

        // MEANINGOFLIFE(8)
        $expression = new TokenizedExpression([
            new Token('MEANINGOFLIFE', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('8', Tokenizer::TYPE_NUMBER),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            50,
            $this->evaluator->evaluate($expression)
        );
    }

    /** @test **/
    function it_passes_the_correct_number_of_arguments_to_the_registered_function()
    {
        $this->evaluator->addFunction('avg', function(...$args){
            return array_sum($args) / count($args);
        });

        $expression = new TokenizedExpression([
            new Token('avg', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('10', Tokenizer::TYPE_NUMBER),
            new Token(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR),
            new Token('20', Tokenizer::TYPE_NUMBER),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            15,
            $this->evaluator->evaluate($expression)
        );
    }

    /** @test **/
    function it_throws_if_the_function_is_not_recognized()
    {
        // FOO(8)
        $expression = new TokenizedExpression([
                new Token('FOO', Tokenizer::TYPE_FUNCTION),
                new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
                new Token('8', Tokenizer::TYPE_NUMBER),
                new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->expectException(EvaluatorException::class);

        $this->evaluator->evaluate($expression);
    }


    /** @test **/
    function it_evaluates_nested_functions()
    {
        $this->mockEvaluatorFunction('AVERAGE')
            ->expects($this->once())
            ->method(self::CALLABLE_METHOD_NAME)
            ->with(10,15,18)
            ->willReturn(14.3);

        $this->mockEvaluatorFunction('ROUND')
                ->expects($this->once())
                ->method(self::CALLABLE_METHOD_NAME)
                ->with(14.3)
                ->willReturn(14);

        $tokens = new TokenizedExpression([
            new Token('ROUND', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('AVERAGE', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('10', Tokenizer::TYPE_NUMBER),
            new Token(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR),
            new Token('15', Tokenizer::TYPE_NUMBER),
            new Token(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR),
            new Token('18', Tokenizer::TYPE_NUMBER),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            14,
            $this->evaluator->evaluate($tokens)
        );
    }


    /** @test **/
    function it_evaluates_nested_functions_and_arithmetic_operations_mixed()
    {
        $this->mockEvaluatorFunction('AVERAGE')
            ->expects($this->once())
            ->method(self::CALLABLE_METHOD_NAME)
            ->with(10,15,18)
            ->willReturn(14.3);

        $this->mockEvaluatorFunction('ROUND')
                ->expects($this->once())
                ->method(self::CALLABLE_METHOD_NAME)
                ->with(28.6)
                ->willReturn(29);

        // ROUND( AVERAGE( 10, 15, 18 ) * 2 )
        $tokens = new TokenizedExpression([
            new Token('ROUND', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('AVERAGE', Tokenizer::TYPE_FUNCTION),
            new Token('(', Tokenizer::TYPE_OPEN_PARENTHESIS),
            new Token('10', Tokenizer::TYPE_NUMBER),
            new Token(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR),
            new Token('15', Tokenizer::TYPE_NUMBER),
            new Token(',', Tokenizer::TYPE_ARGUMENT_SEPARATOR),
            new Token('18', Tokenizer::TYPE_NUMBER),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token('*', Tokenizer::TYPE_OPERATOR),
            new Token('2', Tokenizer::TYPE_NUMBER),
            new Token(')', Tokenizer::TYPE_CLOSING_PARENTHESIS),
        ]);

        $this->assertEquals(
            29,
            $this->evaluator->evaluate($tokens)
        );
    }
}