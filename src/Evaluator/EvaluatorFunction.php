<?php namespace Professor\Evaluator;

class EvaluatorFunction
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var int
     */
    private $minArgs;
    /**
     * @var null
     */
    private $maxArgs;

    /**
     * EvaluatorFunction constructor.
     *
     * @param callable $callback
     * @param int      $minArgs
     * @param null     $maxArgs
     */
    public function __construct(callable $callback, int $minArgs = 0, $maxArgs = null)
    {
        $this->callback = $callback;
        $this->minArgs = $minArgs;
        $this->maxArgs = $maxArgs;
    }

    public function evaluate(array $arguments)
    {
        return call_user_func_array($this->callback, $arguments);
    }
}