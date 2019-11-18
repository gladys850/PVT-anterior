<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanSubmitedDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   //para el caso de los documentos
        Schema::create('loan_submited_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('loan_id')->unsigned();
            $table->foreign('loan_id')->references('id')->on('loans');
			$table->unsignedBigInteger('procedure_requirement_id')->unsigned();
            $table->foreign('procedure_requirement_id')->references('id')->on('procedure_requirements');
			$table->date('reception_date')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('is_valid')->default(false);// por defecto 
            //$table->boolean('is_valid')->default(true);
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
        Schema::dropIfExists('loan_submited_documents');
    }
}
