<?php namespace Professor\Utils;

use Professor\Exceptions\UndefinedVariableException;

class VariableExtractor
{
    public static function extract(string $variable, array $variables)
    {
        $path = explode(".", $variable);

        return static::dive($path, $variables);
    }

    private static function dive(array $path, array $variables)
    {
        $current = array_shift($path);

        if(!isset($variables[$current]))
        {
            throw new UndefinedVariableException($current);
        }

        $value = $variables[$current];

        if(count($path) > 0)
        {
            return static::dive($path,$value);
        }

        return $value;
    }
}