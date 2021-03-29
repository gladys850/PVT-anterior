<?php

use Illuminate\Database\Seeder;
use App\LoanDestiny;
class LoanDestinySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $destinity = [
            [
                'name' => 'LIBRE DISPONIBILIDAD',
                'description' => 'Libre disponibilidad'
            ],  [
                'name' => 'SALUD',
                'description' => 'Salud'
            ], [
                'name' => 'CONSUMO',
                'description' => 'Consumo'
            ], [
                'name' => 'AMPLIACIÓN DE VIVIENDA',
                'description' => 'Ampliación de vivienda'
            ],[
                'name' => 'REFACCIÓN DE VIVIENDA',
                'description' => 'Refacción de vivienda'
            ],[
                'name' => 'REMODELACIÓN DE VIVIENDA',
                'description' => 'Remodelación de vivienda'
            ],[
                'name' => 'OTROS VIVIENDA',
                'description' => 'Otros vivienda'
            ],[
                'name' => 'COMERCIO',
                'description' => 'Comercio'
            ],[
                'name' => 'COMPRA DE AUTOMÓVIL',
                'description' => 'Compra de automóvil'
            ],[
                'name' => 'SERVICIO DE INFRAESTRUCTURA',
                'description' => 'Servicio de infraestructura'
            ],[
                'name' => 'INVERSIÓN',
                'description' => 'Inversión'
            ],[
                'name' => 'REFACCIÓN Y REMODELACIÓN DE VIVIENDA',
                'description' => 'Refacción y remodelación de vivienda'
            ] 
        ];
        foreach ($destinity as $destinity) {
           LoanDestiny::firstOrCreate($destinity);
        }
    }
}
