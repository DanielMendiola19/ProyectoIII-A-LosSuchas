<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordToken extends Model
{
    protected $fillable = ['correo', 'token', 'estado', 'expires_at'];

    public $timestamps = true;

    /**
     * Verifica si el token expiró o ya fue usado
     */
    public function isExpired(): bool
    {
        // Si no está activo, ya se considera expirado
        if ($this->estado !== 'activo') return true;

        // Si la fecha actual pasó de expires_at
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Marca el token como usado
     */
    public function markAsUsed()
    {
        $this->update([
            'estado' => 'usado',
            'expires_at' => Carbon::now() // opcional: poner la fecha de uso
        ]);
    }

    /**
     * Marca el token como expirado
     */
    public function markAsExpired()
    {
        $this->update(['estado' => 'expirado']);
    }
}