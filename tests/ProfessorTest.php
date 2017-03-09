<?php

use Selveo\Professor\Evaluator;
use Selveo\Professor\Lexer;
use Selveo\Professor\Professor;

class ProfessorTest extends TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $lexer;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $evaluator;

    /** @var  Professor */
    protected $professor;

    protected function setUp()
    {
        parent::setUp();
        $this->lexer = $this->createMock(Lexer::class);
        $this->evaluator = $this->createMock(Evaluator::class);
        $this->professor = new Professor($this->lexer, $this->evaluator);
    }

    /** @test **/
    function it_wraps_around_the_evaluator_and_lexer_to_provide_a_single_method_api()
    {
        $tokenizedExpression = new Lexer\TokenizedExpression([]);

        $this->lexer->expects($this->once())
                    ->method('tokenize')
                    ->with('1 + 2')
                    ->willReturn($tokenizedExpression);

        $this->evaluator->expects($this->once())
                        ->method('evaluate')
                        ->with($tokenizedExpression)
                        ->willReturn(3);

        $this->professor->calculate("1 + 2");
    }

    /** @test **/
    function it_need_a_single_acceptance_test_just_for_prince_Knud()
    {
        $professor = $this->newRealProfessor();

        $this->assertEquals(
            5,
            $professor->calculate("2 + 3")
        );

        $this->assertEquals(
            13,
            $professor->calculate("@foo + 3", ["foo" => "10"])
        );

        $professor->addFunction('sum', function(...$args){
            return array_sum($args);
        });

        $this->assertEquals(
            15,
            $professor->calculate("sum(5, 10)")
        );

        $this->assertEquals(
            21,
            $professor->calculate("sum(1,2,3,4,5,6)")
        );

        $this->assertEquals(
            19.5,
            $professor->calculate("sum(1,2,3,4,5,6,-1.5)")
        );

        $professor->addFunction('avg', function(...$args){
            return array_sum($args) / count($args);
        });

        $this->assertEquals(
            15,
            $professor->calculate("avg(10, 20)")
        );
    }

    /** @test **/
    function it_unwinds_arrays_and_puts_them_into_a_function()
    {
        $professor = $this->newRealProfessor();

        $professor->addFunction('AVERAGE', function(){
            return array_sum(func_get_args()) / count(func_get_args());
        });

        $variables = [
            "product" => [
                "name" => "Toothpaste",
                "sales_histories" => [
                    ["quantity" => 30, "duration" => 30],
                    ["quantity" => 90, "duration" => 90],
                    ["quantity" => 60, "duration" => 60],
                    ["quantity" => 100, "duration" => 100],
                ]
            ]
        ];

        $result = $professor->calculate(
            "AVERAGE( [...@product.sales_histories.*.quantity / @product.sales_histories.*.duration ])",
            $variables
        );

        $this->assertEquals(1, $result);

        $variables = [
            "product" => [
                "name" => "Toothpaste",
                "sales_histories" => [
                    ["quantity" => 30, "duration" => 30], // 30 sold last 45 days
                    ["quantity" => 45, "duration" => 90], // 45 sold the last 90 days
                    ["quantity" => 90, "duration" => 120, "offset" => 245] // 90 sold the next 120 days (last year)
                ]
            ],
            "supplier" => [
                "average_lead_time" => 3 // Average days from order placement to first reception
            ]
        ];



        $result = $professor->calculate(
            "AVERAGE( [...@product.sales_histories.*.quantity / @product.sales_histories.*.duration ]) * (30 + @supplier.average_lead_time)",
            $variables
        );

        $this->assertEquals(24.75, $result);
    }

    /**
     * @return Professor
     */
    protected function newRealProfessor(): Professor
    {
        return new Professor(new Lexer, new Evaluator);
    }
}