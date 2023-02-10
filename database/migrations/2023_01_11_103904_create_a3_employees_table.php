<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateA3EmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('a3_employees', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('employeeCode');
            $table->string('completeName');
            $table->string('identifierNumber');
            $table->string('jobTitleDescription')->nullable();
            $table->string('personalemail')->nullable();
            $table->string('personalphone')->nullable();
            $table->dateTime('dropDate')->nullable();
            $table->dateTime('enrolmentDate')->nullable();
            $table->integer('companyCode');
            $table->integer('workplaceCode');
            $table->string('workplaceName');
            $table->string('pdiCentre')->nullable();
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
        Schema::dropIfExists('a3_employees');
    }
}
