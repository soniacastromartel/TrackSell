<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('employee_empleado', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('idEmpleado');
            //$table->integer('employee_id')->unique();
            
            $table->unique(['idEmpleado','employee_id']);
            $table->integer('employee_id')->unsigned();
            $table->timestamps();
            
            //$table->bigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            
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
        Schema::dropIfExists('employee_empleado');
    }
}
