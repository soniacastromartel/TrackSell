<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        for ($i=1 ; $i<=5; $i++){
            DB::table('employees')->insert([
                'username'   => 'empleado'.$i,
                'name'       => 'Empleado'.$i,
                'password'   => Hash::make('123456'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        for ($i=1 ; $i<=5; $i++){
            DB::table('employees')->insert([
                'username'   => 'supervisor'.$i,
                'name'       => 'Supervisor'.$i,
                'rol_id'     => 3,
                'password'   => Hash::make('123456'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
    }
}
