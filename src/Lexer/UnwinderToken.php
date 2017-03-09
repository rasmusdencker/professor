<?php namespace Selveo\Professor\Lexer;

class UnwinderToken extends Token
{
    protected $type = Tokenizer::TYPE_UNWINDER;
    /**
     * @var TokenizedExpression
     */
    private $subExpression;

    /**
     * UnwinderToken constructor.
     *
     * @param TokenizedExpression $subExpression
     */
    public function __construct(TokenizedExpression $subExpression)
    {
        $this->subExpression = $subExpression;
    }

    public function getValue()
    {
        return $this->getSubExpression();
    }

    /**
     * @return TokenizedExpression
     */
    public function getSubExpression(): TokenizedExpression
    {
        return $this->subExpression;
    }
}