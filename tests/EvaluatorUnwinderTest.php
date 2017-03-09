<?php

use Selveo\Professor\Lexer\Token;
use Selveo\Professor\Lexer\TokenizedExpression;
use Selveo\Professor\Lexer\Tokenizer;
use Selveo\Professor\Lexer\UnwinderToken;


class EvaluatorUnwinderTest extends EvaluatorTestCase
{
    /** @test **/
    function it_unpacks_each_element_of_an_array_as_individual_arguments()
    {
        $this->mockEvaluatorFunction('assert')
             ->expects($this->once())
             ->method(self::CALLABLE_METHOD_NAME)
             ->with(1, 2, 3)
             ->willReturn(1);

        $expression = new TokenizedExpression([
            new Token("assert", Tokenizer::TYPE_FUNCTION),
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new UnwinderToken(new TokenizedExpression([
                new Token("foo", Tokenizer::TYPE_VARIABLE)
            ])),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS)
        ]);

        $this->evaluator->evaluate($expression, [
            "foo" => [1,2,3]
        ]);
    }

    /** @test **/
    function it_does_arithmetic_operations_on_unpacked_elements_before_passing_them_onto_the_function()
    {
        $this->mockEvaluatorFunction('assert')
             ->expects($this->once())
             ->method(self::CALLABLE_METHOD_NAME)
             ->with(11, 22, 33);

        $expression = new TokenizedExpression([
            new Token("assert", Tokenizer::TYPE_FUNCTION),
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new UnwinderToken(new TokenizedExpression([
                new Token("foo.*", Tokenizer::TYPE_UNWINDING_VARIABLE),
                new Token("+", Tokenizer::TYPE_OPERATOR),
                new Token("bar.*", Tokenizer::TYPE_UNWINDING_VARIABLE)
            ])),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS)
        ]);

        $this->evaluator->evaluate($expression, [
            "foo" => [
                1, 2, 3
            ],
            "bar" => [
                10, 20, 30
            ]
        ]);

        $this->mockEvaluatorFunction('assert')
             ->expects($this->once())
             ->method(self::CALLABLE_METHOD_NAME)
             ->with(9, 18, 27);

        $expression = new TokenizedExpression([
            new Token("assert", Tokenizer::TYPE_FUNCTION),
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new UnwinderToken(new TokenizedExpression([
                new Token("bar.*", Tokenizer::TYPE_UNWINDING_VARIABLE),
                new Token("-", Tokenizer::TYPE_OPERATOR),
                new Token("foo.*", Tokenizer::TYPE_UNWINDING_VARIABLE)
            ])),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS)
        ]);

        $this->evaluator->evaluate($expression, [
            "foo" => [
                1, 2, 3
            ],
            "bar" => [
                10, 20, 30
            ]
        ]);
    }


    /** @test **/
    function it_evaluates_unwinder_tokens_in_more_complex_scenarios()
    {
        $this->evaluator->addFunction('AVERAGE', function(){
            return array_sum(func_get_args()) / count(func_get_args());
        });

        $expression = new TokenizedExpression([
            new Token("AVERAGE", Tokenizer::TYPE_FUNCTION),
            new Token("(", Tokenizer::TYPE_OPEN_PARENTHESIS),
            new UnwinderToken(new TokenizedExpression([
                new Token("product.sales_histories.*.quantity", Tokenizer::TYPE_UNWINDING_VARIABLE),
                new Token("/", Tokenizer::TYPE_OPERATOR),
                new Token("product.sales_histories.*.duration", Tokenizer::TYPE_UNWINDING_VARIABLE)
            ])),
            new Token(")", Tokenizer::TYPE_CLOSING_PARENTHESIS),
            new Token("*", Tokenizer::TYPE_OPERATOR),
            new Token("3", Tokenizer::TYPE_NUMBER)
//            new Token("supplier.average_lead_time", Tokenizer::TYPE_VARIABLE)
        ]);

        $variables = [
            "product" => [
                "name" => "Toothpaste",
                "sales_histories" => [
                    ["quantity" => 30, "duration" => 30],
                    ["quantity" => 45, "duration" => 90],
                    ["quantity" => 90, "duration" => 120, "offset" => 245]
                ]
            ],
            "supplier" => [
                "average_lead_time" => 3 // Average day from order placement to first reception
            ]
        ];

        $result = $this->evaluator->evaluate($expression, $variables);

        $this->assertEquals(2.25, $result);
    }

}