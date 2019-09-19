<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class LaratrustSetupTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return  void
   */
  public function up()
  {
    // Create table for storing permissions
    Schema::table('permissions', function (Blueprint $table) {
      $table->string('name')->nullable();
      $table->string('display_name')->nullable();
    });

    // Create table for associating permissions to users (Many To Many Polymorphic)
    Schema::create('user_permissions', function (Blueprint $table) {
      $table->unsignedInteger('permission_id');
      $table->unsignedInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users')
        ->onUpdate('cascade')->onDelete('cascade');
      $table->foreign('permission_id')->references('id')->on('permissions')
        ->onUpdate('cascade')->onDelete('cascade');

      $table->primary(['user_id', 'permission_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return  void
   */
  public function down()
  {
    Schema::dropIfExists('user_permissions');
    Schema::table('permissions', function (Blueprint $table) {
      $table->dropColumn(['name', 'display_name']);
    });
  }
}
