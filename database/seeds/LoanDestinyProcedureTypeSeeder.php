<?php

use Illuminate\Database\Seeder;
use App\ProcedureType;
use App\LoanDestiny;

class LoanDestinyProcedureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        $sequences = [
            'Préstamo Anticipo' => [
                ['LIBRE DISPONIBILIDAD'],
                ['SALUD'],           
            ],
        ];$this->create($sequences);

        $sequences = [   
            'Préstamo a corto plazo' => [
                ['CONSUMO'],
                ['SALUD'],  
            ],
        ];
        $this->create($sequences);

        $sequences = [    
            'Préstamo a largo plazo' => [
                ['AMPLIACIÓN DE VIVIENDA'],
                ['REFACCIÓN DE VIVIENDA'],
                ['REMODELACIÓN DE VIVIENDA'],  
                ['OTROS VIVIENDA'], 
                ['COMERCIO'], 
                ['COMPRA DE AUTOMÓVIL'], 
                ['SERVICIO DE INFRAESTRUCTURA'],
                ['SALUD'],                    
            ],
        ];
        $this->create($sequences);

        $sequences = [ 
            'Préstamo hipotecario' => [
                ['INVERSIÓN'],
                ['REFACCIÓN Y REMODELACIÓN DE VIVIENDA'],     
            ]
        ];
        $this->create($sequences);      
    }
    
    public function create($sequences){
        foreach ($sequences as $procedure_type => $sequence) {
            $procedure = ProcedureType::whereName($procedure_type)->first();      
            foreach($sequence as $current ){
                $curr []= LoanDestiny::whereName($current)->first()->id;       
                $procedure->loan_destinies()->sync($curr);        
            }   
        }
    }
}
