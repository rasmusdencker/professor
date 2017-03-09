<?php namespace Selveo\Professor\Utils;

class Arr
{
    public static function flatten(array $array)
    {
        $flattened = [];

        array_walk_recursive($array, function($value) use (&$flattened) {
            $flattened[] = $value;
        });

        return $flattened;
    }

    public static function crossAdd(array $a, array $b)
    {
        $result = [];

        foreach($a as $k => $v)
        {
            $result[] = $v + $b[$k];
        }

        return $result;
    }

    public static function crossSubtract($a, $b)
    {
        $result = [];

        foreach($a as $k => $v)
        {
            $result[] = $v - $b[$k];
        }

        return $result;
    }

    public static function crossDivide($a, $b)
    {
        $result = [];

       foreach($a as $k => $v)
       {
           $result[] = $v / $b[$k];
       }

       return $result;
    }

    public static function crossMultiply($a, $b)
    {
        $result = [];

       foreach($a as $k => $v)
       {
           $result[] = $v * $b[$k];
       }

       return $result;
    }

    public static function crossPower($a, $b)
    {
        $result = [];

       foreach($a as $k => $v)
       {
           $result[] = pow($v, $b[$k]);
       }

       return $result;
    }

}