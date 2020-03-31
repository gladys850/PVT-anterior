<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Module;
use App\Affiliate;
use App\City;
use App\LoanState;
use App\PaymentType;
use App\Loan;
use App\Role;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\DateTime($faker));

    $module = Module::whereName('prestamos')->first();
    $affiliate = Affiliate::whereNull('date_death')->whereNull('reason_death')->whereNull('death_certificate_number')->limit(100)->get()->random();
    $procedure_type = $module->procedure_types->random();
    $procedure_modality = $procedure_type->procedure_modalities->random();
    if ($procedure_type->destinies->count() == 0) {
        do {
            $loan_destiny = factory(App\LoanDestiny::class)->create();
        } while ($loan_destiny->procedure_type_id != $procedure_type->id);
    }
    $amount = intval($faker->numberBetween($procedure_type->interval->minimum_amount,$procedure_type->interval->maximum_amount) / 100) * 100;
    return [
        'disbursable_id' => $affiliate->id,
        'disbursable_type' => 'affiliates',
        'procedure_modality_id' => $module->procedure_types->random()->procedure_modalities->random()->id,
        'request_date' => $faker->dateTimeBetween($startDate = '-4 months', $endDate = '-2 months', $timezone = null),
        'parent_loan_id' => null,
        'parent_reason' => null,
        'disbursement_date' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now', $timezone = null),
        'amount_requested' => $amount,
        'city_id' => City::all()->random(),
        'loan_interest_id' => $procedure_modality->current_interest,
        'loan_state_id' => LoanState::whereName('Desembolsado')->first()->id,
        'amount_approved' => $amount,
        'loan_term' => $faker->numberBetween($procedure_type->interval->minimum_term,$procedure_type->interval->maximum_term),
        'disbursement_type_id' => PaymentType::whereName('Cheque')->first()->id,
        'loan_destiny_id' => $procedure_type->destinies->random()->id,
        'role_id' => Role::whereName('PRE-area-de-recepcion')->first()->id,
        'created_at' => $faker->dateTime($max = 'now'),
        'updated_at' => $faker->dateTime($max = 'now')
    ];
});
