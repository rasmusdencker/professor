<?php use Selveo\Professor\Lexer;
use Selveo\Professor\Lexer\Token;
use Selveo\Professor\Lexer\Tokenizer;
use Selveo\Professor\Lexer\UnwinderToken;

class LexerTest extends TestCase
{
    /** @var Lexer */
    protected $lexer;

    protected function setUp() : void
    {
        parent::setUp();
        $this->lexer = new Lexer;

    }

    /** @test **/
    function it_returns_a_tokenized_expression()
    {
        $this->assertInstanceOf(
            Lexer\TokenizedExpression::class,
            $this->lexer->tokenize('1 + 2')
        );
    }

    /** @test **/
    function it_parses_strings_into_tokens()
    {
        $tokens = $this->lexer->tokenize("1+2");

        $this->assertCount(3, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("1", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());

        $this->assertEquals("+", $tokens[1]->getValue());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[1]->getType());

        $this->assertEquals("2", $tokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[2]->getType());
    }

    /** @test **/
    function it_doesnt_care_about_whitespace()
    {
        $tokens = $this->lexer->tokenize(" 1  +       2   ");

        $this->assertCount(3, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("1", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());

        $this->assertEquals("+", $tokens[1]->getValue());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[1]->getType());

        $this->assertEquals("2", $tokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[2]->getType());
    }

    /** @test **/
    function it_tokenizes_numbers_over_10()
    {
        $tokens = $this->lexer->tokenize("15 + 202");
        $this->assertCount(3, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("15", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());

        $this->assertEquals("+", $tokens[1]->getValue());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[1]->getType());

        $this->assertEquals("202", $tokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[2]->getType());
    }


    /** @test **/
    function it_tokenizes_negative_numbers()
    {
        $tokens = $this->lexer->tokenize("-5");
        $this->assertCount(1, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("-5", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());
    }

//    /** @test **/
//    function it_tokenizes_scientific_notation()
//    {
//        $this->markTestIncomplete(); // TODO
//    }

    /** @test **/
    function it_tokenizes_numbers_with_decimals()
    {
        $tokens = $this->lexer->tokenize("5.0124");
        $this->assertCount(1, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("5.0124", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());
    }

    /** @test **/
    function it_tokenizes_negative_numbers_with_decimals()
    {
        $tokens = $this->lexer->tokenize("-2.234 + 20");

        $this->assertCount(3, $tokens);
        $this->assertContainsOnlyInstancesOf(Token::class, $tokens);

        $this->assertEquals("-2.234", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[0]->getType());

        $this->assertEquals("+", $tokens[1]->getValue());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[1]->getType());

        $this->assertEquals("20", $tokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[2]->getType());
    }

    /** @test **/
    function it_tokenizes_arithmetic_operators()
    {
        $tokens = $this->lexer->tokenize("+-*/");
        $this->assertCount(4, $tokens);
    }

    /** @test **/
    function it_tokenizes_parenthesis()
    {
        $tokens = $this->lexer->tokenize("(1 + 30) * 5");
        $this->assertCount(7, $tokens);

        $tokens = $this->lexer->tokenize("1 + (30 * 5)");
        $this->assertCount(7, $tokens);

        $tokens = $this->lexer->tokenize("((1 + 30) * 5)");
        $this->assertCount(9, $tokens);
    }

    /** @test **/
    function it_tokenizes_function_calls()
    {
        $this->lexer->addFunctionTokenizer("SUM");

        $tokens = $this->lexer->tokenize("SUM(1, 20)");
        $this->assertCount(6, $tokens);

        $this->assertEquals("SUM", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_FUNCTION, $tokens[0]->getType());

        $this->assertEquals(Tokenizer::TYPE_OPEN_PARENTHESIS, $tokens[1]->getType());

        $this->assertEquals("1", $tokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[2]->getType());

        $this->assertEquals(",", $tokens[3]->getValue());
        $this->assertEquals(Tokenizer::TYPE_ARGUMENT_SEPARATOR, $tokens[3]->getType());

        $this->assertEquals("20", $tokens[4]->getValue());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[4]->getType());

        $this->assertEquals(Tokenizer::TYPE_CLOSING_PARENTHESIS, $tokens[5]->getType());
    }

    /** @test **/
    function it_tokenizes_variables()
    {
        $tokens = $this->lexer->tokenize("@foo");

        $this->assertCount(1, $tokens);
        $this->assertEquals("foo", $tokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[0]->getType());

        $tokens = $this->lexer->tokenize("@foo.bar");

        $this->assertCount(1, $tokens);
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[0]->getType());

        $tokens = $this->lexer->tokenize("@foo.bar.baz");

        $this->assertCount(1, $tokens);
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[0]->getType());

        $tokens = $this->lexer->tokenize("@foo.bar  + @foo.baz");

        $this->assertCount(3, $tokens);
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[0]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[1]->getType());
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[0]->getType());
    }

    /** @test **/
    function it_tokenizes_unwinders()
    {
        // =======================================================
        // An unwinder is a special token which unpacks/unwinds an
        // array to separate function arguments.
        // =======================================================
        $tokens = $this->lexer->tokenize("[...@foo]");

        $this->assertCount(1, $tokens);
        $this->assertEquals(Tokenizer::TYPE_UNWINDER, $tokens[0]->getType());
        $this->assertInstanceOf(UnwinderToken::class, $tokens[0]);

        $subExpression = $tokens[0]->getSubExpression();

        $this->assertCount(1, $subExpression);
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $subExpression[0]->getType());
    }

    /** @test **/
    function it_tokenizes_unwinders_internal_expressions()
    {
        // =======================================================
        // An unwinder is a special token which unpacks/unwinds an
        // array to separate function arguments.
        // =======================================================
        $tokens = $this->lexer->tokenize("[...@foo + @bar]");

        $this->assertCount(1, $tokens);
        $this->assertEquals(Tokenizer::TYPE_UNWINDER, $tokens[0]->getType());
        $this->assertInstanceOf(UnwinderToken::class, $tokens[0]);

        $unwinderTokens = $tokens[0]->getSubExpression();
        $this->assertCount(3, $unwinderTokens);

        $this->assertEquals("foo", $unwinderTokens[0]->getValue());
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $unwinderTokens[0]->getType());

        $this->assertEquals("+", $unwinderTokens[1]->getValue());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $unwinderTokens[1]->getType());

        $this->assertEquals("bar", $unwinderTokens[2]->getValue());
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $unwinderTokens[2]->getType());

    }

    /** @test **/
    function it_tokenizes_unwinding_variables()
    {
        //=======================================================
        //An unwinding variable is an array which is wound up and
        //inserted into a function as separate arguments.
        //=======================================================

        $tokens = $this->lexer->tokenize('@foo.*.bar');
        $this->assertCount(1,$tokens);
        $this->assertEquals(Tokenizer::TYPE_UNWINDING_VARIABLE, $tokens[0]->getType());
        $this->assertEquals("foo.*.bar", $tokens[0]->getValue());
    }

    /** @test **/
    function it_tokenizes_unwinders_with_unwinding_variables()
    {
        $tokens = $this->lexer->tokenize("[...@foo.*.bar + @baz.*]");

        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(UnwinderToken::class, $tokens[0]);

        $subTokens = $tokens[0]->getSubExpression();
        $this->assertCount(3, $subTokens);

        $this->assertEquals(Tokenizer::TYPE_UNWINDING_VARIABLE, $subTokens[0]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $subTokens[1]->getType());
        $this->assertEquals(Tokenizer::TYPE_UNWINDING_VARIABLE, $subTokens[2]->getType());
    }

    /** @test **/
    function it_tokenizes_unwinders_in_a_more_complex_expression()
    {
        $this->lexer->addFunctionTokenizer('AVERAGE');

        $tokens = $this->lexer->tokenize("AVERAGE( [...@product.sales_histories.*.quantity / @product.sales_histories.*.duration ]) * @supplier.average_lead_time");
        //AVERAGE
        //(
        //[...@product.sales_histories.*.quantity / @product.sales_histories.*.duration ]
        //)
        //*
        //@supplier.average_lead_time

        $this->assertCount(6, $tokens);

        $this->assertEquals(Tokenizer::TYPE_FUNCTION, $tokens[0]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPEN_PARENTHESIS, $tokens[1]->getType());
        $this->assertEquals(Tokenizer::TYPE_UNWINDER, $tokens[2]->getType());
        $this->assertEquals(Tokenizer::TYPE_CLOSING_PARENTHESIS, $tokens[3]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $tokens[4]->getType());
        $this->assertEquals(Tokenizer::TYPE_VARIABLE, $tokens[5]->getType());

        $unwinderTokens = $tokens[2]->getSubExpression();

        $this->assertCount(3, $unwinderTokens);

        $this->assertEquals(Tokenizer::TYPE_UNWINDING_VARIABLE, $unwinderTokens[0]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPERATOR, $unwinderTokens[1]->getType());
        $this->assertEquals(Tokenizer::TYPE_UNWINDING_VARIABLE, $unwinderTokens[2]->getType());


    }

    /** @test **/
    function it_tokenizes_nested_functions()
    {
        $this->lexer->addFunctionTokenizer('AVERAGE');
        $this->lexer->addFunctionTokenizer('ROUND');

        $tokens = $this->lexer->tokenize("ROUND(AVERAGE( 10, 13 ))");

        $this->assertCount(9, $tokens);

        $this->assertEquals(Tokenizer::TYPE_FUNCTION, $tokens[0]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPEN_PARENTHESIS, $tokens[1]->getType());
        $this->assertEquals(Tokenizer::TYPE_FUNCTION, $tokens[2]->getType());
        $this->assertEquals(Tokenizer::TYPE_OPEN_PARENTHESIS, $tokens[3]->getType());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[4]->getType());
        $this->assertEquals(Tokenizer::TYPE_ARGUMENT_SEPARATOR, $tokens[5]->getType());
        $this->assertEquals(Tokenizer::TYPE_NUMBER, $tokens[6]->getType());
        $this->assertEquals(Tokenizer::TYPE_CLOSING_PARENTHESIS, $tokens[7]->getType());
        $this->assertEquals(Tokenizer::TYPE_CLOSING_PARENTHESIS, $tokens[8]->getType());
    }
}