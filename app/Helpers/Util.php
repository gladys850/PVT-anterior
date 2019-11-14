<?php

namespace App\Helpers;

class Util
{
    public static function translate($string)
    {
        $attributes = include "resources/lang/es/validation.php";
        $attributes = $attributes['attributes'];
        return $attributes[$string];
    }
}