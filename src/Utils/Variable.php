<?php namespace Professor\Utils;

class Variable
{
    public static function add($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return Arr::crossAdd($a, $b);
        }

        return $a + $b;
    }

    public static function subtract($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return Arr::crossSubtract($a, $b);
        }

        return $a - $b;
    }

    public static function divide($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return Arr::crossDivide($a, $b);
        }

        return $a / $b;
    }

    public static function multiply($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return Arr::crossMultiply($a, $b);
        }

        return $a * $b;
    }

    public static function pow($a, $b)
    {
        if (is_array($a) && is_array($b)) {
            return Arr::crossPower($a, $b);
        }

        return pow($a, $b);
    }
}