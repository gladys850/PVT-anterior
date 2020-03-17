<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Module;
use App\LoanDestiny;
use Faker\Generator as Faker;

$factory->define(LoanDestiny::class, function (Faker $faker) {
    $module = Module::whereName('prestamos')->first();
    return [
        'procedure_type_id' => $module->procedure_types->random()->id,
        'name' => $faker->unique()->word,
        'description' => $faker->sentence($nbWords = 5, $variableNbWords = true),
        'created_at' => $faker->dateTime($max = 'now'),
        'updated_at' => $faker->dateTime($max = 'now')
    ];
});
