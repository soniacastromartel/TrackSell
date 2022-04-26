<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewValidationrrhh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('validation_rrhh', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('cod_business');
            $table->integer('cod_employee');
            $table->string('dni');
            $table->string('centre');
            $table->string('name');
            $table->date('cancellation_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->double('total_income');
            $table->integer('employee_id');
            $table->string('tracking_ids')->nullable();
            $table->integer('is_supervisor');
            $table->double('total_super_incentive');
            $table->string('month_year');
            $table->timestamps();
        });
    }
                    // dni           AS '1',
                    // centre        AS '1',
                    // employee      AS '1',
                    // cancellation_date as '1', 
                    // total_income  as '1',
                    // paid_date     as '1'
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        //DB::statement('DROP VIEW IF EXISTS pdi2.validation_rrhh');
        Schema::dropIfExists('validation_rrhh');
    }
}
