<?php

use Illuminate\Database\Seeder;
use App\Module;
use App\LoanState;

class LoanStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            [['name' => 'Mora'],['description' => 'Retraso de la cuota a la fecha establecido']],
            [['name' => 'En Proceso'],['description' => 'Prestamo en tramite' ]],
            [['name' => 'Aprobado '],['description' => 'Solicitud de prestamo Aprobado' ]],
            [['name' => 'Anulado '],['description' => 'Tramite del prestamo Anulado' ]],
            [['name' => 'Rechazado '],['description' => 'Tramite del prestamo Rechazado' ]],
            [['name' => 'Desembolsado '],['description' => 'Dinero Desembolsado' ]],
            [['name' => 'Liquidado '],['description' => 'Pago de la deuda en su totalidad' ]],
            [['name' => 'Amortizando '],['description' => 'Pagando Cuotas' ]]
            
        ];
        foreach ($states as $state) {
            LoanState::firstOrcreate($state[0],$state[1]);
        }
        
    }
}
