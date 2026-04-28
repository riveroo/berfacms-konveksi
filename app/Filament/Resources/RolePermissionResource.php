<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Models\Role;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $modelLabel = 'Role & Permission';
    protected static ?string $pluralModelLabel = 'Roles & Permissions';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->canAccess('roles', 'read');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->columnSpanFull(),
                Forms\Components\ViewField::make('permissions_matrix')
                    ->label('Permissions')
                    ->view('filament.forms.components.permission-matrix')
                    ->columnSpanFull()
                    ->default(function () {
                        $menus = ['products', 'transactions', 'pre_orders', 'reports', 'inventory', 'users', 'roles'];
                        return array_map(function ($menu) {
                            return [
                                'menu_name' => $menu,
                                'can_read' => false,
                                'can_add' => false,
                                'can_edit' => false,
                                'can_delete' => false,
                            ];
                        }, $menus);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->rowIndex()->label('No'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Role Name'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Assigned Permissions'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Is Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->mutateRecordDataUsing(function (\Illuminate\Database\Eloquent\Model $record, array $data): array {
                        $matrix = [];
                        $menus = ['products', 'transactions', 'pre_orders', 'reports', 'inventory', 'users', 'roles'];
                        
                        foreach ($menus as $menu) {
                            $perm = $record->permissions->where('menu_name', $menu)->first();
                            $matrix[] = [
                                'menu_name' => $menu,
                                'can_read' => $perm ? $perm->can_read : false,
                                'can_add' => $perm ? $perm->can_add : false,
                                'can_edit' => $perm ? $perm->can_edit : false,
                                'can_delete' => $perm ? $perm->can_delete : false,
                            ];
                        }
                        $data['permissions_matrix'] = $matrix;
                        return $data;
                    })
                    ->using(function (\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {
                        $matrix = $data['permissions_matrix'] ?? [];
                        unset($data['permissions_matrix']);
                        
                        $record->update($data);
                        
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
                        $record->permissions()->sync($permissionIds);
                        
                        return $record;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRolePermissions::route('/'),
        ];
    }
}
