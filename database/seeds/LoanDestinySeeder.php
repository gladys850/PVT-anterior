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
                'name' => 'Libre disponibilidad',
                'description' => 'Libre disponibilidad'
            ],  [
                'name' => 'Salud',
                'description' => 'Salud'
            ], [
                'name' => 'Consumo',
                'description' => 'Consumo'
            ], [
                'name' => 'Ampliación de vivienda',
                'description' => 'Ampliación de vivienda'
            ],[
                'name' => 'Refacción de vivienda',
                'description' => 'Refacción de vivienda'
            ],[
                'name' => 'Remodelación de vivienda',
                'description' => 'Remodelación de vivienda'
            ],[
                'name' => 'Otros vivienda',
                'description' => 'Otros vivienda'
            ],[
                'name' => 'Comercio',
                'description' => 'Comercio'
            ],[
                'name' => 'Compra de automóvil',
                'description' => 'Compra de automóvil'
            ],[
                'name' => 'Servicio de infraestructura',
                'description' => 'Servicio de infraestructura'
            ],[
                'name' => 'Inversión',
                'description' => 'Inversión'
            ],[
                'name' => 'Refacción y remodelación de vivienda',
                'description' => 'Refacción y remodelación de vivienda'
            ] 
        ];
        foreach ($destinity as $destinity) {
            $new_modality=  LoanDestiny::firstOrCreate($destinity);
        }
    }
}
