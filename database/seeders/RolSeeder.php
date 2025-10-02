<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Administrador', 'Cajero', 'Cocinero', 'Mesero'];

        foreach ($roles as $rol) {
            Rol::create(['nombre' => $rol]);
        }
    }
}