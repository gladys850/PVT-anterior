<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPrintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_prints', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_type_id'); // id tipo modalidad
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types');
            $table->unsignedBigInteger('role_id'); // id rol
            $table->foreign('role_id')->references('id')->on('roles');
            $table->boolean('print')->default('false');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_prints');
    }
}
