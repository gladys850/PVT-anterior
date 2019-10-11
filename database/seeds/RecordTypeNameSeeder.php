<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\RecordType;

class RecordTypeNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $record_types = RecordType::get();
        foreach ($record_types as $record_type)
        {
            $record_type->update(['name' => Str::slug($record_type->display_name, '-')]);
        }
    }
}