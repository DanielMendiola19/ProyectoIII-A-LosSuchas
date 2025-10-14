<?php

namespace App\Jobs;

use App\Events\TokenExpired;
use App\Models\PasswordToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;

    public function __construct(PasswordToken $token)
    {
        $this->token = $token;
    }

    public function handle()
    {
        if ($this->token->estado === 'activo') {
            $this->token->update(['estado' => 'expirado']);
            broadcast(new TokenExpired($this->token->correo));
        }
    }
}

