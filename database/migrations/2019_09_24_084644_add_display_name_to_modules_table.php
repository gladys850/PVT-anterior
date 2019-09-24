<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddDisplayNameToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->renameColumn('name', 'display_name');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        $all = json_decode(DB::table('modules')->get(), true);
        foreach ($all as $item)
        {
            DB::table('modules')->where('id', $item['id'])->update(['name' => Str::slug($item['display_name'], '-')]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->renameColumn('display_name', 'name');
        });
    }
}