<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Bebidas', 'descripcion' => 'Cafés, jugos y otras bebidas'],
            ['nombre' => 'Comida', 'descripcion' => 'Alimentos como sandwiches y croissants'],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces, pasteles y repostería'],
            ['nombre' => 'Snacks', 'descripcion' => 'Aperitivos y bocadillos'],
            ['nombre' => 'Promociones', 'descripcion' => 'Ofertas y combos especiales'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
