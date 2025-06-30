<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejemplo: empleados
        // for ($i = 1; $i <= 5; $i++) {
        //     DB::table('employees')->insert([
        //         'username'   => 'empleado' . $i,
        //         'name'       => 'Empleado' . $i,
        //         'password'   => Hash::make('123456'),
        //         'created_at' => now(),
        //     ]);
        // }

        // Supervisores
        // for ($i = 1; $i <= 5; $i++) {
        //     DB::table('employees')->insert([
        //         'username'   => 'supervisor' . $i,
        //         'name'       => 'Supervisor' . $i,
        //         'rol_id'     => 3,
        //         'password'   => Hash::make('123456'),
        //         'created_at' => now(),
        //     ]);
        // }

        $this->call(DepartmentsSeeder::class);
    }
}
