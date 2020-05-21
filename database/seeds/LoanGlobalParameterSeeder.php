<?php

use Illuminate\Database\Seeder;
use App\LoanGlobalParameter;

class LoanGlobalParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $global_parameters = [
            ['offset_ballot_day' => 7, 'offset_interest_day' => 15, 'livelihood_amount' => 510, 'min_service_years' =>1, 'min_service_years_adm' =>2, 'max_guarantor_active' =>3, 'max_guarantor_passive' =>2]
        ];
        foreach ($global_parameters as $global_parameter) {
            LoanGlobalParameter::firstOrCreate($global_parameter);
        }
    }
}
