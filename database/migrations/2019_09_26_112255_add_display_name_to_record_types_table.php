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
