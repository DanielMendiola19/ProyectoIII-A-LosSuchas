<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Comprobar si el usuario ya existe para no duplicarlo
        if (!Usuario::where('correo', 'admin@gmail.com')->exists()) {
            Usuario::create([
                'nombre' => 'Alfredo',
                'apellido' => 'Cortez',
                'correo' => 'admin@gmail.com',
                'contrasena' => Hash::make('Admin_123'),
                'rol_id' => 1, // Administrador
            ]);
        }
    }
}