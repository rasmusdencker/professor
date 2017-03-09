<?php namespace Selveo\Professor\Lexer;

class Tokenizer
{
    const TYPE_OPERATOR = "operator";
    const TYPE_NUMBER   = "number";
    const TYPE_OPEN_PARENTHESIS = "open parenthesis";
    const TYPE_CLOSING_PARENTHESIS = "closing parenthesis";
    const TYPE_FUNCTION = "function";
    const TYPE_ARGUMENT_SEPARATOR = "argument separator";
    const TYPE_VARIABLE = "variable";
    const TYPE_UNWINDER = "unwinder";
    const TYPE_UNWINDING_VARIABLE = "unwinding variable";

    /**
     * @var string
     */
    protected $pattern;
    /**
     * @var string
     */
    protected $type;

    /**
     * Tokenizer constructor.
     *
     * @param string $pattern
     * @param string $type
     */
    public function __construct(string $pattern, string $type)
    {
        $this->pattern = $pattern;
        $this->type = $type;
    }

    public function matcher()
    {
        // ===========================================================
        // If the matcher is autocapturing (i.e. has a capture group),
        // don't wrap it in a matcher.
        // ===========================================================
        if(preg_match('/(?<!\\\)\((?!\?\:)/', $this->pattern))
        {
            return "/{$this->pattern}/";
        }

        return "/({$this->pattern})/";
    }

    public function type()
    {
        return $this->type;
    }


}