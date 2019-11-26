<?php

namespace App\Helpers;

use Carbon;
use Config;
use App\RecordType;
use Illuminate\Support\Str;

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
        } else {
            try {
                $value = Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y');
            } catch (\Exception $e) {}
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
        $path = app_path() . '/resources/lang/es/validation.php';
        if(@include $path) {
            $translations_file = include(app_path().'/resources/lang/es/validation.php');
        }
        if (isset($translations_file)) {
            if (array_key_exists($string, $translations_file['attributes'])) {
                return $translations_file['attributes'][$string];
            }
        }
        return $string;
    }

    public static function round($value)
    {
        return round($value, 2, PHP_ROUND_HALF_EVEN);
    }

    public static function money_format($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public static function pivot_action($relationName, $pivotIds, $message)
    {
        $action = $message . ' ';
        $action .= self::translate($relationName) . ': ';
        if (substr($relationName, 0, 4) != 'App\\') {
            $relationName = 'App\\'.Str::studly(strtolower(Str::singular($relationName)));
        }
        if (is_subclass_of($relationName, 'Illuminate\Database\Eloquent\Model')) {
            $action .= '(';
            foreach ($pivotIds as $id) {
                $action .= app($relationName)::find($id)->display_name;
                if (next($pivotIds)) {
                    $action .= ', ';
                } else {
                    $action .= ')';
                }
            }
        }
        return $action;
    }

    public static function concat_action($object, $message = 'editó')
    {
        $old = app(get_class($object));
        $old->fill($object->getOriginal());
        $action = $message;
        $updated_values = $object->getDirty();
        $relationships = $object->relationships();
        foreach ($updated_values as $key => $value) {
            $display_names = ['display_name', 'name', 'code', 'shortened', 'number', 'correlative', 'description'];
            $concat = false;
            $action .= ' [' . Util::translate($key) . '] ';
            if (substr($key, -3, 3) == '_id') {
                $attribute = substr($key, 0, -3);
                if (array_key_exists($attribute, $relationships)) {
                    if ($relationships[$attribute]['type'] == 'BelongsTo') {
                        $old_relation = app($relationships[$attribute]['model'])::find($old[$key]);
                        $new_relation = app($relationships[$attribute]['model'])::find($value);
                        if ($old_relation) {
                            foreach ($display_names as $title) {
                                if (isset($old_relation[$title])) {
                                    $action .= $old_relation[$title];
                                    break;
                                }
                            }
                        }
                        $action .= ' -> ';
                        if ($new_relation) {
                            foreach ($display_names as $title) {
                                if (isset($new_relation[$title])) {
                                    $action .= $new_relation[$title];
                                    break;
                                }
                            }
                        }
                        $concat = true;
                    }
                }
            }
            if (!$concat) {
                $action .= Util::bool_to_string($old[$key]) . ' -> ' . Util::bool_to_string($object[$key]);
            }
            if (next($updated_values)) {
                $action .= ', ';
            }
        }
        return $action;
    }

    public static function save_record($object, $type, $action)
    {
        $record_type = RecordType::whereName($type)->first();
        if ($record_type) {
            $record = $object->records()->make([
                'action' => $action
            ]);
            $record->record_type()->associate($record_type);
            $record->save();
        }
    }
}