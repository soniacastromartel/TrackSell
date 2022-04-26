<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_changes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('start_date');
            $table->date('end_date');

            $table->integer('created_user_id')->unsigned();
            $table->foreign('created_user_id')->references('id')->on('employees');

            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->integer('centre_origin_id')->unsigned()->nullable();
            $table->foreign('centre_origin_id')->references('id')->on('centres');

            $table->integer('centre_destination_id')->unsigned()->nullable();
            $table->foreign('centre_destination_id')->references('id')->on('centres');

            $table->boolean('validated')->nullable();
            $table->integer('validate_user_id')->unsigned()->nullable();
            $table->foreign('validate_user_id')->references('id')->on('employees');

            $table->text('observations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_changes');
    }
}
