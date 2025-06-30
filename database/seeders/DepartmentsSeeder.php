<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) id del centro padre (lo buscamos por si algún día cambia)
        $parentCentre = DB::table('centres')
            ->where('id', 28) // o ->where('name', 'HOSPITAL TELDE')
            ->select('id')
            ->first();

        if (!$parentCentre) {
            $this->command->error('Centro padre (id 28) no encontrado. Seeder abortado.');
            return;
        }

        $parentId = $parentCentre->id;

        // 2) Centros que van a convertirse en departamentos
        $childCentres = DB::table('centres')
            ->where('label', 'HOSPITAL CIUDAD DE TELDE')
            ->where('id', '!=', $parentId)   // excluye el padre
            ->select('id', 'name', 'email')
            ->get();

        if ($childCentres->isEmpty()) {
            $this->command->warn('No se encontraron centros-hijo con label HOSPITAL CIUDAD DE TELDE.');
            return;
        }

        // 3) Insertamos departamentos si aún no existen
        foreach ($childCentres as $centre) {
            // Evita duplicados: ¿ya existe un departamento con ese centre_id?
            $exists = DB::table('departments')
                ->where('centre_id', $centre->id)
                ->exists();

            if ($exists) {
                $this->command->info("Departamento para centre_id {$centre->id} ya existe. Saltando.");
                continue;
            }

            DB::table('departments')->insert([
                'centre_id'  => $centre->id,   
                'parent_id'  => $parentId,     
                'name'       => $centre->name, 
                'email'      => $centre->email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("Departamento {$centre->name} creado (centre_id {$centre->id}).");
        }
    }
}
