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
            $table->string('shortened')->nullable();
        });
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