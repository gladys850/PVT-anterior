<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementFundRotatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_fund_rotatories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id')->nullable();  // id loan prestamo
            $table->dateTime('date_check_delivery')->nullable(); ;
          //  $table->string('check_number'); 
            $table->string('description');
            $table->float('entry_amount',10,2)->default(0);
            $table->float('output_amount',10,2)->default(0);
            $table->float('balance',10,2); //saldo
            $table->string('movement_concept_code')->unique();
            $table->unique(['movement_concept_code', 'date_check_delivery']);
            $table->unsignedBigInteger('movement_concept_id')->nullable();
            $table->foreign('movement_concept_id')->references('id')->on('movement_concepts');
            $table->unsignedBigInteger('user_id');  // id usuario
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('role_id');  // id del rol de usuario
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movement_fund_rotatories');
    }
}
