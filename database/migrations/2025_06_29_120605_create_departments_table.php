<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedInteger('centre_id');
            $table->foreign('centre_id')
                ->references('id')
                ->on('centres')
                ->onDelete('cascade');

            // supervisor_id: conserva foreignId() *solo si* users.id es bigint
            // Si users.id también es int, aplica el mismo patrón que centre_id
            $table->foreignId('supervisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // parent_id sigue bien (departments.id es bigint)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('departments')
                ->cascadeOnDelete();

            // Data columns
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();

            // (Optional) keep one department name unique per centre
            $table->unique(['centre_id', 'name']);
        });

        /*
        |-------------------------------------------------------------------------
        | Seed the initial HCT departments
        |-------------------------------------------------------------------------
        | We resolve the centre ID for “HOSPITAL TELDE” only once, then reuse it.
        */
        $centreId = DB::table('centres')
            ->where('name', 'HOSPITAL TELDE')
            ->value('id');

        if ($centreId) {
            DB::table('departments')->insert([
                [
                    'centre_id'   => $centreId,
                    'name'        => 'HCT - Administración',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'centre_id'   => $centreId,
                    'name'        => 'HCT - Enfermería',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'centre_id'   => $centreId,
                    'name'        => 'HCT - Radiología',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
                [
                    'centre_id'   => $centreId,
                    'name'        => 'HCT - Rehabilitación',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
