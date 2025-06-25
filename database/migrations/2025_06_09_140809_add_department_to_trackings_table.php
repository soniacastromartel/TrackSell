<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentToTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/xxxx_xx_xx_add_department_to_trackings_table.php
   public function up()
{
    Schema::table('trackings', function (Blueprint $table) {
        if (!Schema::hasColumn('trackings', 'department')) {
$table->foreignId('department')->nullable()->constrained('departments')->nullOnDelete();
        }
    });
}


    public function down()
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropForeign(['department']);
            $table->dropColumn('department');
        });
    }


}