<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Painel Administrativo';
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response && Filament::auth()->check()) {
            Filament::auth()->user()->update([
                'last_login_at' => now(),
            ]);
        }

        return $response;
    }
}
