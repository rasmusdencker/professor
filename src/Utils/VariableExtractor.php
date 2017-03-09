<?php namespace Selveo\Professor\Utils;

use Selveo\Professor\Exceptions\UndefinedVariableException;

class VariableExtractor
{
    const VARIABLE_WILDCARD = "*";

    public static function extract(string $variable, array $variables)
    {
        $path = explode(".", $variable);

        return static::dive($path, $variables);
    }

    private static function dive(array $path, array $variables)
    {
        $current = array_shift($path);

        if($current === self::VARIABLE_WILDCARD)
        {
            return static::diveAll($path, $variables);
        }

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

    private static function diveAll($path, $variables)
    {
        $result = [];

        if(count($path) > 0)
        {
            foreach($variables as $value)
            {
                $result[] = static::dive($path, $value);
            }

            return Arr::flatten($result);
        }

        foreach($variables as $value)
        {
            $result[] = $value;
        }

        return $result;
    }
}