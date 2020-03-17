<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Module;
use App\Affiliate;
use App\City;
use App\LoanState;
use App\PaymentType;
use App\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    $module = Module::whereName('prestamos')->first();
    $affiliate = Affiliate::whereNull('date_death')->whereNull('reason_death')->whereNull('death_certificate_number')->limit(100)->get()->random();
    $disbursement_date = $faker->dateTimeBetween($startDate = '-3 months', $endDate = '-1 months', $timezone = null);
    $procedure_type = $module->procedure_types->random();
    $procedure_modality = $procedure_type->procedure_modalities->random();
    if ($procedure_type->destinys->count() == 0) {
        do {
            $loan_destiny = factory(App\LoanDestiny::class)->create();
        } while ($loan_destiny->procedure_type_id != $procedure_type->id);
    }
    $amount = intval($faker->numberBetween($procedure_type->interval->minimum_amount,$procedure_type->interval->maximum_amount) / 100) * 100;
    return [
        'disbursable_id' => $affiliate->id,
        'disbursable_type' => 'affiliates',
        'procedure_modality_id' => $module->procedure_types->random()->procedure_modalities->random()->id,
        'disbursement_date' => $disbursement_date,
        'parent_loan_id' => null,
        'parent_reason' => null,
        'request_date' => Carbon::parse($disbursement_date)->subMonth(),
        'amount_requested' => $amount,
        'city_id' => City::all()->random(),
        'loan_interest_id' => $procedure_modality->current_interest,
        'loan_state_id' => LoanState::whereName('Desembolsado')->first()->id,
        'amount_approved' => $amount,
        'loan_term' => $faker->numberBetween($procedure_type->interval->minimum_term,$procedure_type->interval->maximum_term),
        'disbursement_type_id' => PaymentType::whereName('Cheque')->first()->id,
        'loan_destiny_id' => $procedure_type->destinys->random()->id,
        'created_at' => $faker->dateTime($max = 'now'),
        'updated_at' => $faker->dateTime($max = 'now')
    ];
});
