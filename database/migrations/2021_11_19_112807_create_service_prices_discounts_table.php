<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePricesDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_prices_discounts', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('service_price_id')->unsigned();
            $table->foreign('service_price_id')->references('id')->on('service_prices');

            $table->string('discount_type'); 
            $table->double('price', 8, 2)->nullable();

            $table->double('direct_incentive', 8, 2)->nullable();
            
            $table->double('incentive1', 8, 2)->nullable();
            $table->double('incentive2', 8, 2)->nullable();
            
            $table->double('super_incentive1', 8, 2)->nullable();
            $table->double('super_incentive2', 8, 2)->nullable();

            $table->date('cancellation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_prices_discounts');
    }
}
