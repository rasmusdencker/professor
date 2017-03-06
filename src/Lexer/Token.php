<?php namespace Professor\Lexer;

class Token
{
    private $value;
    /**
     * @var string
     */
    private $type;

    /**
     * Token constructor.
     *
     * @param        $value
     * @param string $type
     */
    public function __construct($value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}