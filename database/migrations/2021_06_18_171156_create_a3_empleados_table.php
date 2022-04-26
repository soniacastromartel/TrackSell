<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateA3EmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a3_empleados', function (Blueprint $table) {
            $table->id();

            $table->string('Nombre_Completo');
            $table->string('Email')->nullable();
            $table->string('Telefono')->nullable();
            $table->string('Codigo_Centro')->nullable();
            $table->string('Codigo_Empleado')->nullable();
            $table->string('Codigo_Empresa')->nullable();
            $table->string('Categoria')->nullable();
            $table->string('Nombre_Empresa')->nullable();
            $table->string('NIF');
            $table->string('Fecha_de_Alta_en_compañia')->nullable();
            $table->string('Fecha_de_baja_en_compañia')->nullable();
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
        Schema::dropIfExists('a3_empleados');
    }
}
