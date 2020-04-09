<?php

use Illuminate\Database\Seeder;
use App\LoanDestiny;
use App\ProcedureType;

class LoanDestinySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedure = ProcedureType::whereName('Préstamo Anticipo')->first();
        $anticipos = [
            ['procedure_type_id' => $procedure->id, 'name' => 'Consumo', 'description' => 'Consumo Personal' ],
            ['procedure_type_id' => $procedure->id, 'name' => 'Salud', 'description' => 'Salud Personal' ],
        ];
        foreach($anticipos as $anticipo) {
            LoanDestiny::firstOrCreate($anticipo);
        }
    }
}
