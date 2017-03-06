<?php

use Professor\Utils\VariableExtractor as E;

class VariableExtractorTest extends TestCase
{
    /** @test **/
    function it_retrieves_a_variable_from_an_array()
    {
        $this->assertEquals(
            "50",
            E::extract("foo", ["foo" => "50"])
        );
    }

    /** @test **/
    function it_retrieves_a_variable_from_an_array_using_dot_notation()
    {
        $this->assertEquals(
            "50",
            E::extract("foo.bar", [
                "foo" => [
                    "bar" => "50"
                ]
            ])
        );
    }

    /** @test **/
    function it_throws_if_a_variable_is_not_found()
    {
        $this->expectException(\Professor\Exceptions\UndefinedVariableException::class);
        E::extract("foo", []);
    }
}