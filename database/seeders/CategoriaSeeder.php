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
            ['nombre' => 'Combos', 'descripcion' => 'Combos especiales'],
            ['nombre' => 'Promociones', 'descripcion' => 'Ofertas especiales'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}