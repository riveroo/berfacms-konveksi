<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRolePermissions extends ManageRecords
{
    protected static string $resource = RolePermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->using(function (array $data, string $model): \Illuminate\Database\Eloquent\Model {
                    $matrix = $data['permissions_matrix'] ?? [];
                    unset($data['permissions_matrix']);
                    
                    $role = $model::create($data);
                    
                    $permissionIds = [];
                    foreach ($matrix as $row) {
                        $perm = \App\Models\Permission::firstOrCreate([
                            'menu_name' => $row['menu_name'],
                            'can_read' => $row['can_read'],
                            'can_add' => $row['can_add'],
                            'can_edit' => $row['can_edit'],
                            'can_delete' => $row['can_delete'],
                        ]);
                        $permissionIds[] = $perm->id;
                    }
                    $role->permissions()->sync($permissionIds);
                    
                    return $role;
                }),
        ];
    }
}
