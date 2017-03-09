<?php

use Professor\Utils\Arr;

class ArrayHelperTest extends TestCase
{
    /** @test **/
    function it_cross_adds_arrays()
    {
        $result = Arr::crossAdd(
            [10,20],
            [1,2]
        );

        $this->assertEquals([11,22], $result);
    }

    /** @test **/
    function it_cross_subtracts_arrays()
    {
        $result = Arr::crossSubtract(
            [10,20],
            [1,2]
        );

        $this->assertEquals([9,18], $result);
    }


    /** @test **/
    function it_cross_divides_arrays()
    {
        $result = Arr::crossDivide(
            [10,20],
            [1,2]
        );

        $this->assertEquals([10,10], $result);
    }

    /** @test **/
    function it_cross_multiplies_arrays_aka_calculates_the_cartesian_product()
    {
        $result = Arr::crossMultiply(
            [10,20],
            [1,2]
        );

        $this->assertEquals([10,40], $result);
    }
}