<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('centres', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->string('address')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('email')->nullable(); 
            $table->string('timetable')->nullable(); 
            $table->string('island')->nullable(); 
            $table->string('alias_img')->nullable(); 
            $table->string('image')->nullable();
            $table->unsignedInteger('parent_id')->nullable(); 
            $table->dateTime('cancellation_date')->nullable();
            $table->timestamps();

            // Foreign key for parent_id
            $table->foreign('parent_id')->references('id')->on('centres')->onDelete('set null');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('centres');
        Schema::enableForeignKeyConstraints();
    }
}
