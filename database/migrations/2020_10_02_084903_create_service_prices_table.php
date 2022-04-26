<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('service_prices', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->double('price', 8, 2);

            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('services');

            $table->integer('centre_id')->unsigned()->nullable();
            $table->foreign('centre_id')->references('id')->on('centres');

            $table->double('service_price_direct_incentive', 8, 2)->nullable();
            
            $table->double('service_price_incentive1', 8, 2)->nullable();
            $table->double('service_price_incentive2', 8, 2)->nullable();
            
            $table->double('service_price_super_incentive1', 8, 2)->nullable();
            $table->double('service_price_super_incentive2', 8, 2)->nullable();

            $table->date('cancellation_date')->nullable();
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
        Schema::dropIfExists('service_prices');
    }
}
