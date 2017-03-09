<?php namespace Selveo\Professor\Utils;

class RangeCoverage
{
    /** @var array */
    protected $coverage;

    /**
     * RangeCoverage constructor.
     */
    public function __construct(int $start, int $length)
    {
        $this->coverage = array_fill($start, $length, false);
    }


    public function isPartlyCovered(int $start, int $length = 1) : bool
    {
        return count( array_filter( array_slice($this->coverage, $start, $length) ) ) > 0;
    }

    /**
     * @param int $start
     * @param int $length
     *
     * @return $this
     */
    public function markCovered(int $start, int $length = 1)
    {
        array_splice($this->coverage,$start,$length,array_fill($start,$length,true));
        return $this;
    }
}