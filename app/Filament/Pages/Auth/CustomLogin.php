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
}
