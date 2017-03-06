<?php

use Professor\Evaluator;
use Professor\Lexer;
use Professor\Professor;

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
        $professor = new Professor(new Lexer, new Evaluator);

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
}