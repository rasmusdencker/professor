<?php

use Professor\Evaluator;

class EvaluatorTestCase extends TestCase
{
    /** @var  Evaluator */
    protected $evaluator;

    public function setUp()
    {
        parent::setUp();
        $this->evaluator = new Evaluator;
    }
}