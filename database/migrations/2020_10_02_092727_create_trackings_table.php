<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->string('hc', 45)->nullable();
            $table->string('patient_name', 100);
            $table->date('started_date');
            $table->boolean('apointment_done')->nullable();
            $table->date('apointment_date')->nullable();
            $table->boolean('service_done')->nullable();
            $table->date('service_date')->nullable();
            $table->boolean('invoiced_done')->nullable();
            $table->date('invoiced_date')->nullable();
            $table->boolean('validation_done')->nullable();
            $table->date('validation_date')->nullable();
            $table->date('cancellation_date')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->integer('started_user_id');
            $table->integer('apointment_user_id')->nullable();
            $table->integer('service_user_id')->nullable();
            $table->integer('invoiced_user_id')->nullable();
            $table->integer('validation_user_id')->nullable();
            $table->integer('cancellation_user_id')->nullable();
            $table->timestamps();
            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services');
            //$table->foreignId('centre_id')->constrained('centres');

            $table->integer('centre_id')->unsigned()->nullable();
            $table->foreign('centre_id')->references('id')->on('centres');

            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->integer('centre_employee_id')->unsigned()->nullable();
            $table->foreign('centre_employee_id')->references('id')->on('centres');

            $table->foreign('started_user_id')->references('id')->on('employees');
            $table->foreign('apointment_user_id')->references('id')->on('employees');
            $table->foreign('service_user_id')->references('id')->on('employees');
            $table->foreign('invoiced_user_id')->references('id')->on('employees');
            $table->foreign('validation_user_id')->references('id')->on('employees');
            $table->foreign('cancellation_user_id')->references('id')->on('employees');
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
        Schema::dropIfExists('trackings');
    }
}
