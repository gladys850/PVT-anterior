<?php

namespace App\Helpers;

use Config;

class Util
{
    public static function bool_to_string($value)
    {
        if (is_bool($value)) {
            if ($value) {
                $value = 'SI';
            } else {
                $value = 'NO';
            }
        }
        return $value;
    }

    public static function translate($string)
    {
        $translation = static::translate_table($string);
        if ($translation) {
            return $translation;
        } else {
            return static::translate_attribute($string);
        }
    }

    public static function translate_table($string)
    {
        if (array_key_exists($string, Config::get('translations'))) {
            return Config::get('translations')[$string];
        } else {
            return null;
        }
    }

    public static function translate_attribute($string)
    {
        $translations_file = include(app_path().'/resources/lang/es/validation.php');
        if (array_key_exists($string, $translations_file['attributes'])) {
            return $translations_file['attributes'][$string];
        } else {
            return null;
        }
    }
}