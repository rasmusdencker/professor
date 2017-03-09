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


    /** @test **/
    function it_extracts_subelements_with_a_wildcard_at_the_end()
    {
        $result = E::extract('foo.*', [
            "foo" => [
                1,2,3
            ]
        ]);

        $this->assertEquals([1,2,3], $result);
    }


    /** @test **/
    function it_extracts_subelements_with_a_wildcard_in_the_middle()
    {
        $result = E::extract('foo.*.bar', [
            "foo" => [
                [
                    "bar" => 12,
                    "boom" => 34
                ],
                [
                    "bar" => 56,
                    "baz" => 78
                ]
            ]
        ]);

        $this->assertEquals([12,56], $result);

        $result = E::extract('foo.*.bar.*.baz', [
            "foo" => [
                [
                    "bar" => [
                        [ "baz" => 23]
                    ],
                    "boom" => 34
                ],
                [
                    "bar" => [
                        [ "baz" => 10]
                    ],
                    "baz" => 78
                ]
            ]
        ]);

        $this->assertEquals([23,10], $result);
    }
}