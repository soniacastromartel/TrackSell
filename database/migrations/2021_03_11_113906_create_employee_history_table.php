<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_history', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->integer('centre_id')->unsigned()->nullable();
            $table->foreign('centre_id')->references('id')->on('centres');
            
            $table->integer('rol_id')->unsigned()->nullable();
            $table->foreign('rol_id')->references('id')->on('roles');

            $table->dateTime('cancellation_date')->nullable();

            //fecha_activo
            //cancel is null on cancel_date Str::between($subject, 'from', 'to');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_history');
    }
}
