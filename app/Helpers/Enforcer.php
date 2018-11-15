<?php

namespace App\Helpers;

class Enforcer
{
    public static function __add($class, $c)
    {
        $reflection = new \ReflectionClass($class);
        $constantsForced = $reflection->getConstants();
        foreach ($constantsForced as $constant => $value) {
            if (constant("$c::$constant") == "abstract") {
                throw new \Exception("Undefined $constant in " . (string) $c);
            }
        }
    }
}
