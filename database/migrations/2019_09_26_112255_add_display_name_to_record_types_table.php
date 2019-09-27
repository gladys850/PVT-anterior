<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddDisplayNameToRecordTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('record_types', function (Blueprint $table) {
            $table->renameColumn('name', 'display_name');
        });

        Schema::table('record_types', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        $all = json_decode(DB::table('record_types')->get(), true);
        foreach ($all as $item)
        {
            DB::table('record_types')->where('id', $item['id'])->update(['name' => Str::slug($item['display_name'], '-')]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('record_types', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('record_types', function (Blueprint $table) {
            $table->renameColumn('display_name', 'name');
        });
    }
}
