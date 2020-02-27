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
            ['offset_day' => '7', 'livelihood_amount' => '510']
        ];
        foreach ($global_parameters as $global_parameter) {
            LoanGlobalParameter::firstOrCreate($global_parameter);
        }
    }
}
