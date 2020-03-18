<?php
use Selveo\Professor\Evaluator;

class CallableMock{
    public function call()
    {

    }
}

class EvaluatorTestCase extends TestCase
{
    const CALLABLE_METHOD_NAME = 'call';

    /** @var  Evaluator */
    protected $evaluator;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluator = new Evaluator;
    }

    /**
     * @param $functionName
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEvaluatorFunction($functionName = "assert"): CallableMock
    {
        $mock = $this->createMock(CallableMock::class);

        $this->evaluator->addFunction($functionName, [$mock, self::CALLABLE_METHOD_NAME]);

        return $mock;
    }
}