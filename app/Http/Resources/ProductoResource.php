<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request)
    {
        $imagen = $this->imagen;
        $imagenUrl = null;

        if ($imagen) {
            // si guardaste con disk 'public' (store(...,'public'))
            $imagenUrl = asset('storage/' . $imagen);
        }

        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio' => (float) $this->precio,
            'stock' => (int) $this->stock,
            'categoria' => $this->whenLoaded('categoria', function() {
                return ['id'=>$this->categoria->id,'nombre'=>$this->categoria->nombre];
            }),
            'imagen' => $imagen,
            'imagen_url' => $imagenUrl,
        ];
    }
}
