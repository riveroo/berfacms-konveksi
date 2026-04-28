<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    /**
     * Override the default view for the login page
     */
    protected static string $view = 'filament.pages.auth.login';

    /**
     * Override the default layout so we have full screen control
     */
    protected static string $layout = 'filament-panels::components.layout.base';

    /**
     * Override credentials to only allow active users
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => true,
        ];
    }

    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        $response = parent::authenticate();

        $user = filament()->auth()->user();

        if ($user && $user->role && !$user->role->is_active) {
            filament()->auth()->logout();
            $this->throwFailureValidationException();
        }

        return $response;
    }
}
