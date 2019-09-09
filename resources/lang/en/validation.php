<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    
    'accepted'       => 'El campo :attribute debe aceptarse.',
    'active_url'       => 'El campo :attribute no es una URL válida.',
    'after'        => 'El campo :attribute debe ser una fecha posterior a la fecha :date.',
    'after_or_equal'     => 'El campo :attribute debe ser una fecha posterior o igual a la fecha :date.',
    'alpha'        => 'El campo :attribute solo puede contener letras.',
    'alpha_dash'       => 'El campo :attribute solo puede contener letras, números, y guiones.',
    'alpha_num'      => 'El campo :attribute solo puede contener letras y números.',
    'array'        => 'El campo :attribute debe ser una lista.',
    'before'         => 'El campo :attribute debe ser una fecha anterior a la fecha :date.',
    'before_or_equal'    => 'El campo :attribute debe ser una fecha anterior o igual a la fecha :date.',
    'between'        => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file'  => 'El campo :attribute debe estar entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute debe estar entre :min y :max caracteres.',
        'array'   => 'El campo :attribute debe tener entre :min y :max ítems.',
    ],
    'boolean'        => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'      => 'El campo :attribute no se encuentra confirmado.',
    'date'         => 'El campo :attribute no es una fecha válida.',
    'date_format'      => 'El campo :attribute no coincide con el formato :format.',
    'different'      => 'El campo :attribute y :other deben ser diferentes.',
    'digits'         => 'El campo :attribute debe ser de :digits dígitos.',
    'digits_between'     => 'El campo :attribute debe estar entre :min y :max dígitoss.',
    'dimensions'       => 'El campo :attribute contiene dimensiones inválidas de imagen.',
    'distinct'       => 'El campo :attribute tiene una valor duplicado.',
    'email'        => 'El campo :attribute debe ser una dirección de correo.',
    'exists'         => 'La selección :attribute es inválida.',
    'file'         => 'El campo :attribute debe ser un archivo.',
    'filled'         => 'El campo :attribute debe contener un valor.',
    'gt'           => [
        'numeric' => 'El campo :attribute debe ser mayor a :value.',
        'file'  => 'El campo :attribute debe ser mayor a :value kilobytes.',
        'string'  => 'El campo :attribute debe ser mayor a :value caracteres.',
        'array'   => 'El campo :attribute debe tener mas de :value ítems.',
    ],
    'gte'          => [
        'numeric' => 'El campo :attribute debe ser mayor o igual a :value.',
        'file'  => 'El campo :attribute debe ser mayor o igual a :value kilobytes.',
        'string'  => 'El campo :attribute debe ser mayor o igual a :value caracteres.',
        'array'   => 'El campo :attribute debe tener :value ítems o más.',
    ],
    'image'        => 'El campo :attribute debe ser una imagen.',
    'in'           => 'La selección :attribute es inválida.',
    'in_array'       => 'El campo :attribute no existe en :other.',
    'integer'        => 'El campo :attribute debe ser un entero.',
    'ip'           => 'El campo :attribute debe ser una dirección IP.',
    'ipv4'         => 'El campo :attribute debe ser una dirección IPv4.',
    'ipv6'         => 'El campo :attribute debe ser una dirección IPv6.',
    'json'         => 'El campo :attribute debe ser una cadena JSON.',
    'lt'           => [
        'numeric' => 'El campo :attribute debe ser menor a :value.',
        'file'  => 'El campo :attribute debe ser menor a :value kilobytes.',
        'string'  => 'El campo :attribute debe ser menor a :value caracteres.',
        'array'   => 'El campo :attribute debe tener menos de :value ítems.',
    ],
    'lte'          => [
        'numeric' => 'El campo :attribute debe ser menor a or equal :value.',
        'file'  => 'El campo :attribute debe ser menor a or equal :value kilobytes.',
        'string'  => 'El campo :attribute debe ser menor a or equal :value caracteres.',
        'array'   => 'El campo :attribute must not have more than :value items.',
    ],
    'max'          => [
        'numeric' => 'El campo :attribute may not be greater than :max.',
        'file'  => 'El campo :attribute may not be greater than :max kilobytes.',
        'string'  => 'El campo :attribute may not be greater than :max caracteres.',
        'array'   => 'El campo :attribute may not have more than :max items.',
    ],
    'mimes'        => 'El campo :attribute debe ser un archivo of type: :values.',
    'mimetypes'      => 'El campo :attribute debe ser un archivo of type: :values.',
    'min'          => [
        'numeric' => 'El campo :attribute debe ser at least :min.',
        'file'  => 'El campo :attribute debe ser at least :min kilobytes.',
        'string'  => 'El campo :attribute debe ser at least :min caracteres.',
        'array'   => 'El campo :attribute debe tener at least :min items.',
    ],
    'not_in'         => 'La selección :attribute es inválida.',
    'not_regex'      => 'El campo :attribute format es inválida.',
    'numeric'        => 'El campo :attribute debe ser a number.',
    'present'        => 'El campo :attribute field debe ser present.',
    'regex'        => 'El campo :attribute format es inválida.',
    'required'       => 'El campo :attribute field is required.',
    'required_if'      => 'El campo :attribute field is required when :other is :value.',
    'required_unless'    => 'El campo :attribute field is required unless :other is in :values.',
    'required_with'    => 'El campo :attribute field is required when :values is present.',
    'required_with_all'  => 'El campo :attribute field is required when :values is present.',
    'required_without'   => 'El campo :attribute field is required when :values is not present.',
    'required_without_all' => 'El campo :attribute field is required when none of :values are present.',
    'same'         => 'El campo :attribute y :other must match.',
    'size'         => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file'  => 'El campo :attribute debe ser :size kilobytes.',
        'string'  => 'El campo :attribute debe ser :size caracteres.',
        'array'   => 'El campo :attribute must contain :size items.',
    ],
    'string'         => 'El campo :attribute debe ser a string.',
    'timezone'       => 'El campo :attribute debe ser  zone.',
    'unique'         => 'El campo :attribute has already been taken.',
    'uploaded'       => 'El campo :attribute failed to upload.',
    'url'          => 'El campo :attribute format es inválida.',
    
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    
    'attributes' => [],
    
];
