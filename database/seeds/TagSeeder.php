<?php

use Illuminate\Database\Seeder;
use App\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            ['name' => 'Mora', 'shortened' => 'Mora', 'slug' => 'Retraso de la cuota a la fecha establecido'],
            ['name' => 'Aprobado', 'shortened' => 'Aprobado', 'slug' => 'Solicitud de prestamo Aprobado'],
            ['name' => 'Amortizando', 'shortened' => 'Amortizando', 'slug' => 'Pagando Cuotas']
        ];
        foreach ($tags as $tag) {
            Tag::firstOrCreate($tag);
        }
    }
}
