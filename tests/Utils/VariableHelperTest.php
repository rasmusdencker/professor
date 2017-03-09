<?php

use Selveo\Professor\Utils\Variable as VariableHelper;

class VariableHelperTest extends TestCase
{
    /** @test * */
    function it_sums_numbers_and_arrays()
    {
        $a = 1;
        $b = 2;

        $this->assertEquals(
            3,
            VariableHelper::add($a, $b)
        );

        $a = [1, 2];
        $b = [3, 4];

        $this->assertEquals(
            [4, 6],
            VariableHelper::add($a, $b)
        );
    }

    /** @test * */
    function it_multiplies_numbers_and_arrays()
    {
        $a = 3;
        $b = 2;

        $this->assertEquals(
            6,
            VariableHelper::multiply($a, $b)
        );

        $a = [1, 2];
        $b = [3, 4];

        $this->assertEquals(
            [3, 8],
            VariableHelper::multiply($a, $b)
        );
    }

    /** @test * */
    function it_divides_numbers_and_arrays()
    {
        $a = 10;
        $b = 2;

        $this->assertEquals(
            5,
            VariableHelper::divide($a, $b)
        );

        $a = [12, 20];
        $b = [3, 5];

        $this->assertEquals(
            [4, 4],
            VariableHelper::divide($a, $b)
        );
    }

    /** @test **/
    function it_subtracts_numbers_and_arrays()
    {
        $a = 10;
        $b = 2;

        $this->assertEquals(
            8,
            VariableHelper::subtract($a, $b)
        );

        $a = [12, 20];
        $b = [3, 5];

        $this->assertEquals(
            [9, 15],
            VariableHelper::subtract($a, $b)
        );
    }

    /** @test **/
    function it_raises_numbers_and_arrays_to_a_power()
    {
        $a = 10;
        $b = 2;

        $this->assertEquals(
            100,
            VariableHelper::pow($a, $b)
        );

        $a = [12, 20];
        $b = [3, 2];

        $this->assertEquals(
            [1728, 400],
            VariableHelper::pow($a, $b)
        );
    }
}