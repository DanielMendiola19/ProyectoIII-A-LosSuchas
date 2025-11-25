<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class DefaultUsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombre' => 'Alejandro',
                'apellido' => 'Flores',
                'correo' => 'cocinero@gmail.com',
                'rol' => 'Cocinero'
            ],
            [
                'nombre' => 'Daniel',
                'apellido' => 'Mendiola',
                'correo' => 'cajero@gmail.com',
                'rol' => 'Cajero'
            ],
            [
                'nombre' => 'Leandro',
                'apellido' => 'Bolaños',
                'correo' => 'mesero@gmail.com',
                'rol' => 'Mesero'
            ],
        ];

        foreach ($usuarios as $u) {
            $rol = Rol::where('nombre', $u['rol'])->first();
            if($rol && !Usuario::where('correo', $u['correo'])->exists()) {
                Usuario::create([
                    'nombre' => $u['nombre'],
                    'apellido' => $u['apellido'],
                    'correo' => $u['correo'],
                    'contrasena' => Hash::make('User_123'),
                    'rol_id' => $rol->id,
                ]);
            }
        }
    }
}
