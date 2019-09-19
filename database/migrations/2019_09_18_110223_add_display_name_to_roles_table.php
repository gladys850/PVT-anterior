<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddDisplayNameToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('name', 'display_name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        $all = json_decode(DB::table('roles')->get(), true);
        foreach ($all as $item)
        {
            DB::table('roles')->where('id', $item['id'])->update(['name' => Str::slug($item['display_name'], '-')]);
        }
        DB::table('roles')->where('display_name', 'Administrador')->update(['name' => 'admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('display_name', 'name');
        });
    }
}
