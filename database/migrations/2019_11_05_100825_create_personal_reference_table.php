<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_references', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('loan_id'); // id persona de referencia
            $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable(); 
            $table->string('cell_phone_number')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_references');
    }
}
