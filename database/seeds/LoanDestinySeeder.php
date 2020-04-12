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
        $destinies = [
            [
                'name' => 'Consumo',
                'description' => 'Consumo Personal',
                'procedures' => ['Préstamo Anticipo']
            ], [
                'name' => 'Salud',
                'description' => 'Salud Personal',
                'procedures' => ['Préstamo Anticipo']
            ]
        ];
        foreach ($destinies as $destiny) {
            $new_destiny = LoanDestiny::firstOrCreate([
                'name' => $destiny['name']
            ], [
                'description' => $destiny['description']
            ]);
            $procedures = ProcedureType::whereIn('name', $destiny['procedures'])->pluck('id');
            $new_destiny->procedure_types()->sync($procedures);
        }
    }
}
