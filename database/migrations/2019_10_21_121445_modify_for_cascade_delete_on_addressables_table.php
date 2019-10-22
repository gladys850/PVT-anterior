<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForCascadeDeleteOnAddressablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addressables', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->foreign('address_id')->references('id')->on('addresses')->onUpdate('cascade')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addressables', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }
}
