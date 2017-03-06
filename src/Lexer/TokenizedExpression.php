<?php namespace Professor\Lexer;

class TokenizedExpression implements \Countable, \IteratorAggregate, \ArrayAccess
{
    protected $tokens = [];

    /**
     * TokenizedExpression constructor.
     *
     * @param array $tokens
     */
    public function __construct(array $tokens)
    {
        array_map([$this, 'addToken'], $tokens);
    }


    public function addToken(Token $token)
    {
        $this->tokens[] = $token;

        return $this;
    }

    public function count()
    {
        return count($this->tokens);
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     * @codeCoverageIgnore
     */
    public function offsetExists($offset)
    {
        return isset($this->tokens[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @codeCoverageIgnore
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->tokens[$offset];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @codeCoverageIgnore
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->tokens[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @codeCoverageIgnore
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->tokens[$offset]);
    }


    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     * @codeCoverageIgnore
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->tokens);
    }
}