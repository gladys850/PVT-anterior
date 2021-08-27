<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_concepts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('shortened')->unique();
            $table->string('description');
            $table->enum('type',['INGRESO', 'EGRESO']);
            $table->string('abbreviated_supporting_document');
            $table->unique(['name', 'type','abbreviated_supporting_document']);
            $table->boolean('is_valid')->default(true);
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
        Schema::dropIfExists('movement_concepts');
    }
}
