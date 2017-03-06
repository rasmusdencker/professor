<?php namespace Professor\Utils;

class StringCoverage extends RangeCoverage
{
    /**
     * StringCoverage constructor.
     */
    public function __construct(string $string)
    {
        parent::__construct(0, strlen($string));
    }


}