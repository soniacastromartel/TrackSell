<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChamanSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chaman_sync', function (Blueprint $table) {
            $table->id();
            $table->text('pdi_request')->nullable();
            $table->string('response')->nullable();
            $table->string('SoliPres')->nullable();
            $table->string('State')->nullable();
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
        Schema::dropIfExists('chaman_sync');
    }
}
