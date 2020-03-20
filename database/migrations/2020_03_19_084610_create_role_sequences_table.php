<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_sequences', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_type_id'); // id flujo de trabajo para tipo de trámite
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types');
            $table->unsignedBigInteger('role_id'); // id rol actual
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedBigInteger('next_role_id'); // id rol siguiente
            $table->foreign('next_role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_sequences');
    }
}