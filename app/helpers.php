<?php

if (!function_exists('canAccessMenu')) {
    function canAccessMenu($route)
    {
        $user = auth()->user();
        if (!$user || !$user->role || !$user->role->is_active) {
            return false;
        }

        return $user->role->permissions->contains(function ($permission) use ($route) {
            return $permission->route === $route && $permission->can_access === true;
        });
    }
}
