<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('employees', function (Blueprint $table) {
            
            $table->increments('id')->unsigned();
            $table->string('objectguid')->nullable();
            $table->string('name')->unique();
            $table->dateTime('cancellation_date')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->integer('user_id')->unique()->nullable();

            $table->integer('centre_id')->unsigned()->nullable();
            $table->foreign('centre_id')->references('id')->on('centres');

            $table->integer('rol_id')->unsigned()->default(2);
            $table->foreign('rol_id')->references('id')->on('roles');
            
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
