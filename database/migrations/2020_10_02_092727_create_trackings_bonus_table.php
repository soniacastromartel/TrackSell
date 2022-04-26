<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('trackings_bonus', function (Blueprint $table) {
            $table->id();
            
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->double('total_income', 8, 2);
            $table->boolean('paid_done')->nullable();
            $table->date('paid_date')->nullable();
            $table->integer('paid_user_id')->unsigned();
            $table->foreign('paid_user_id')->references('id')->on('employees');
            $table->string('month_year');
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
        Schema::dropIfExists('trackings_bonus');
    }
}
