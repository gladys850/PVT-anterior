<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LoanDestiny;
use Faker\Generator as Faker;

$factory->define(LoanDestiny::class, function (Faker $faker) {
    return [
        'name' => mb_strtoupper($faker->unique()->word),
        'description' => $faker->sentence($nbWords = 5, $variableNbWords = true),
        'created_at' => $faker->dateTime($max = 'now'),
        'updated_at' => $faker->dateTime($max = 'now')
    ];
});
